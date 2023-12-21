import './bootstrap';

import flatpickr from "flatpickr";
import { Portuguese } from "flatpickr/dist/l10n/pt.js"
import 'flatpickr/dist/flatpickr.min.css'

// import Alpine from 'alpinejs';
// window.Alpine = Alpine;

import './../../vendor/power-components/livewire-powergrid/dist/powergrid'
 
// If you use Tailwind 
import './../../vendor/power-components/livewire-powergrid/dist/tailwind.css'

// If you use Bootstrap 5 
// import './../../vendor/power-components/livewire-powergrid/dist/bootstrap5.css'

// Alpine.start();

// On page load or when changing themes, best to add inline in `head` to avoid FOUC
window.refreshTheme = () => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}

// window.refreshTheme();

window.toggleTheme = (value = null) => {
    if(value) {
        if(value == 'light' || value == 'dark') {
            localStorage.theme = value;
        }else{
            localStorage.removeItem('theme')
        }
    }else{
        localStorage.theme = localStorage.theme == 'light' ? 'dark' : 'light';
    }
}

document.addEventListener('livewire:init', () => {
    console.log('livewire:init');
    window.addEventListener('setFocus', function(e){
        if(!e.detail.length) return;

        setTimeout(function () {
            let detail = e.detail[0];
            let element;

            if(detail.query != undefined)   element = document.querySelector(detail.query);
            else if(detail.id != undefined) element = document.getElementById(detail.id);

            if(element) {
                if(detail.select != undefined && detail.select == true) element.select();

                let time = (detail.time != undefined) ? detail.time : 0;
                setTimeout(function () { element.focus() }, time);
            }
        }, 150);
    });
});

document.addEventListener('livewire:initialized', () => {
    console.log('livewire:initialized');
});

// window.setFocus = (value = null) => {
//     let element = document.getElementById(value);
//     if(element) {
//         console.log(value, element);
//         document.getElementById(value).focus();
//     }
// }