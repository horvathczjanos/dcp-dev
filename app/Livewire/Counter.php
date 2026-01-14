<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Counter extends Component
{
    public $counter = 1;

    #[On('decrement')] 
    public function evenetProc(){
        if ($this->counter < -5) {
            $this->counter = 0;
        } else {
            
        }
    }

    public function increment()
    {

        if ($this->counter == 5) {
            $this->counter = 1;
        } else {
            $this->counter++;
        }
    }

    public function descrement()
    {
        $this->counter--;

        $this->dispatch('decrement'); 
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
