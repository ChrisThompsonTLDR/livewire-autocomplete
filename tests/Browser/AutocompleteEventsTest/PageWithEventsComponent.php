<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteEventsTest;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Component;

class PageWithEventsComponent extends Component
{
    public $results = [
        'bob',
        'john',
        'bill'
    ];

    public $input = '';

    public $selected;

    public $options;

    public function calculateResults()
    {
        $this->reset('results');

        $this->results = Collection::wrap($this->results)
            ->filter(function ($result) {
                if (!$this->input) {
                    return true;
                }

                return str_contains($result, $this->input);
            })
            ->values()
            ->toArray();
    }

    public function updatedInput()
    {
        $this->calculateResults();
    }

    public function updatedSelected()
    {
        $this->input = $this->selected ?? null;

        $this->calculateResults();
    }

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <div>
                    <x-lwc::autocomplete
                        name="item"
                        wire:input-property="input"
                        wire:selected-property="selected"
                        wire:results-property="results"
                        wire:options-property="options"
                        />
                </div>

                <div dusk="result-output">{{ $selected }}</div>

                <div dusk="options">{{ $options }}</div>

                <div
                    x-data="{ selected: null, input: null }"
                    dusk="alpine-output"
                    x-on:item-input.window="input = $event.detail"
                    x-on:item-selected.window="selected = $event.detail"
                    x-on:item-cleared.window="selected = null; input = null"
                    >
                    <div>
                        Alpine Input: <span dusk="alpine-input" x-text="input"></span>
                    </div>

                    <div>
                        Alpine Selected: <span dusk="alpine-selected" x-text="selected"></span>
                    </div>
                    <button dusk="alpine-clear" x-on:click="$dispatch('item-clear')">Alpine Clear</button>
                    <button dusk="alpine-set" x-on:click="$dispatch('item-set', 'bob')">Alpine Clear</button>
                    <button dusk="alpine-options" x-on:click="$dispatch('item-set-options', 'filter')">Alpine Options</button>
                </div>
            </div>
            HTML;
    }
}