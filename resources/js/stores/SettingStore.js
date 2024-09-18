import axios from "axios";
import { defineStore } from "pinia";
import { ref } from "vue";
import { useStorage } from '@vueuse/core';
import { getActiveLanguage } from "laravel-vue-i18n";
import { loadLanguageAsync } from 'laravel-vue-i18n';

export const useSettingStore = defineStore('SettingStore', () => {
    const settings = ref({
        app: {name: ''},
    });

    const theme = useStorage('SettingStore:theme', ref('light'));

    const changeTheme = () => {
        theme.value = theme.value === 'light' ? 'dark' : 'light';
    };

    const getSettings = async () => {
        await axios.get('/api/settings/fetch')
            .then((response) => {
                settings.value = response.data;
                console.log('STORE setting, response.data: ', response.data);
                console.log('STORE setting: setting.value', settings.value);
            });
    };

    const currlang = ref(getActiveLanguage());

    const applyLang = () => {
        loadLanguageAsync(currlang.value);
    }

    const setLocal = (lang) => {
        axios.post('/api/locale', {
            language: lang
        })
            .then((response) => {
                currlang.value = response.data;
                applyLang();
            })
    };

    const getLocal = async () => {
        await axios.get('/api/locale')
            .then((response) => {
                currlang.value = response.data;
                applyLang();
            })
    };

    return { settings, getSettings, theme, changeTheme, currlang, setLocal, getLocal };
});
