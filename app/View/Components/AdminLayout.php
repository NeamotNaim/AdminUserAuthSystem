<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdminLayout extends Component//admin layout 
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('admin.layouts.app');//admin layouts app
    }
}
