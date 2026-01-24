<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class AddAvailability extends Form
{
    public $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    #[Validate('required|array|min:1')]
    public $selectedDays = [];

    #[Validate('required|date_format:H:i')]
    public $startTime;

    #[Validate('required|date_format:H:i|after:startTime')]
    public $endTime;

    #[Validate('required|string|max:255')]
    public $scheduleName;

}
