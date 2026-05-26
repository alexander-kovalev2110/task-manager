<?php

namespace App\Application\QueryHandler;

use App\Application\Query\SuggestSubtasksQuery;
use App\Domain\Task\TaskRepositoryInterface;
use OpenAI\Client;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SuggestSubtasksHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private Client $openai
    ) {}

    /**
     * @return string[]
     */
    public function __invoke(SuggestSubtasksQuery $query): array
    {
        $task = $this->taskRepository->findById($query->taskId);

        if (!$task) {
            throw new \DomainException('Task not found');
        }

        $prompt = sprintf(
            "Generate 5 concrete, actionable steps or subtasks for a task with the title: '%s'. " .
            "You must return ONLY a raw JSON array of strings (e.g. [\"Step 1\", \"Step 2\", ...]). " .
            "Do not return markdown, code blocks (such as ```json), or any introductory/concluding text. " .
            "Just the raw JSON array.",
            $task->getTitle()
        );

        $response = $this->openai->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.7,
        ]);

        $content = $response->choices[0]->message->content ?? '';
        $json = trim($content);

        // Strip markdown code blocks if the model wrapped the JSON in them
        if (str_starts_with($json, '```json')) {
            $json = substr($json, 7);
        } elseif (str_starts_with($json, '```')) {
            $json = substr($json, 3);
        }
        if (str_ends_with($json, '```')) {
            $json = substr($json, 0, -3);
        }
        $json = trim($json);

        $subtasks = json_decode($json, true);

        if (!is_array($subtasks)) {
            throw new \RuntimeException('Failed to parse OpenAI response: ' . $content);
        }

        return array_map('strval', $subtasks);
    }
}
