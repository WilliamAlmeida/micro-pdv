<?php

namespace App\Traits;

trait HelperActions
{
    public function set_focus($values, $select=false)
    {
        if(is_array($values)) {
            if(isset($values['button'])) {
                if($values['button'] == 'confirm') {
                    $this->set_focus(['query' => '[x-ref="accept"] button']);
                }else{
                    $this->set_focus(['query' => '[x-ref="reject"] button']);
                }
            }else{
                $this->dispatch('setFocus', $values);
            }
        }else if(is_string($values)) {
            $this->dispatch('setFocus', ['id' => $values, 'select' => $select]);
        }
    }
}
