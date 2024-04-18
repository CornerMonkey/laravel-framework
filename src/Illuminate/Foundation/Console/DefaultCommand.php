<?php

namespace Illuminate\Foundation\Console;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use function Laravel\Prompts\search;

#[AsCommand(name: '|_default', description: 'Internal command to provide autocomplete options whe no command supplied')]
class DefaultCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $commands = collect($this->getApplication()->all())->reject(fn (Command $command) => Str::startsWith($command->getName(), '_'))->map(fn(Command $command) => $command->getName());
        $id = search(
            label: 'Search for the command to run',
            placeholder: 'E.g. list',
            options: fn (string $value) => strlen($value) > 0
                ? $commands->filter(fn(string $command) => Str::contains($command, $value))->toArray()
                : $commands->toArray(),
            hint: 'The user will receive an email immediately.'
        );

        $this->getApplication()->getDefinition()->setArguments([]);
        return Artisan::handle(new ArrayInput([$id]),  new ConsoleOutput);
    }
}
