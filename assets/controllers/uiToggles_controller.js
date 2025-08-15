import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static values = {
        initialized: { type: Boolean, default: false }
    }
    connect()
    {
        document.addEventListener('turbo:load', () => {
            if (!this.initializedValue) {
                this.initializedValue = true;
                this.initializeToggles();
            }
        });
    }
    initializeToggles()
    {
        // Dark/Light mode toggle
        const toggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        function setTheme(theme)
        {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            if (themeIcon) {
                if (theme === 'dark') {
                    themeIcon.classList.remove('bi-sun');
                    themeIcon.classList.add('bi-moon');
                } else {
                    themeIcon.classList.remove('bi-moon');
                    themeIcon.classList.add('bi-sun');
                }
            }
        }

        function toggleTheme()
        {
            const current = document.documentElement.getAttribute('data-theme');
            setTheme(current === 'dark' ? 'light' : 'dark');
        }

        // Simple/Complet mode toggle
        const modeBtn = document.getElementById('mode-toggle');
        const modeIcon = document.getElementById('mode-icon');
        const expenseList = document.getElementsByClassName('home-expense-list');

        function setMode(mode)
        {
            localStorage.setItem('sidepanel_mode', mode);
            if (mode === 'complet') {
                for (let el of expenseList) {
                    el.style.display = '';
                }
                if (modeIcon) {
                    modeIcon.classList.remove('bi-list');
                    modeIcon.classList.add('bi-layout-text-sidebar-reverse');
                }
            } else {
                for (let el of expenseList) {
                    el.style.display = 'none';
                }
                if (modeIcon) {
                    modeIcon.classList.remove('bi-layout-text-sidebar-reverse');
                    modeIcon.classList.add('bi-list');
                }
            }
        }

        function toggleMode()
        {
            const current = localStorage.getItem('sidepanel_mode') || 'simple';
            setMode(current === 'complet' ? 'simple' : 'complet');
        }


        // Theme toggle
        let theme = localStorage.getItem('theme');
        if (!theme) {
            theme = prefersDark ? 'dark' : 'light';
        }
        setTheme(theme);
        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleTheme);
        }

        // Mode toggle
        let mode = localStorage.getItem('sidepanel_mode');
        if (!mode) {
            mode = 'simple';
        }
        setMode(mode);
        if (modeBtn) {
            modeBtn.addEventListener('click', toggleMode);
        }

    }
}