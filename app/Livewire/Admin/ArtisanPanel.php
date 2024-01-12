<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Traits\HelperActions;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\Validate;

class ArtisanPanel extends Component
{
    use HelperActions;

    public $artisanPanelModal = false;

    #[Validate('required|min:3', as: 'comando')]
    public $command;
    public $parameters;

    #[Locked]
    public $output;

    #[On('openArtisanPanel')]
    public function openArtisanPanel()
    {
        $this->js('$openModal("artisanPanelModal")');

        $this->reset('command', 'parameters', 'output');

        $this->set_focus('artisan_command', true);
    }

    public function runCommand()
    {
        $this->reset('output');

        $this->validate();

        $params = explode(';', $this->parameters);
        if(empty($this->parameters)) $params = [];

        try {
            throw_unless(auth()->user()->is_admin, 'Only admin can use commands.');

            Artisan::call($this->command, $params);

            $this->output = nl2br(Artisan::output());
        } catch (\Throwable $th) {
            //throw $th;

            $this->output = $th->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.artisan-panel');
    }
}
