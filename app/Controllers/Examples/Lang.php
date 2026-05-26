<?php

namespace App\Controllers\Examples;

use App\Controllers\BaseController;

class Lang extends BaseController
{
    private array $supported = ['ko' => '한국어', 'en' => 'English', 'ja' => '日本語'];

    public function index(): string
    {
        $lang = session()->get('playground_lang') ?? 'ko';
        service('language')->setLocale($lang);

        $keys = [
            'greeting', 'welcome', 'current_lang', 'home', 'about',
            'contact', 'login', 'logout', 'save', 'cancel', 'delete',
            'error', 'success',
        ];

        $translations = [];
        foreach ($keys as $key) {
            $translations[$key] = lang("Playground.{$key}");
        }

        $withParams = [
            lang('Playground.items_found', [42]),
            lang('Playground.page_of', [3, 10]),
            lang('Playground.last_updated', [date('Y-m-d H:i')]),
        ];

        return view('examples/lang/index', [
            'title'        => '다국어 (i18n)',
            'supported'    => $this->supported,
            'currentLang'  => $lang,
            'translations' => $translations,
            'withParams'   => $withParams,
        ]);
    }

    public function switchLang(string $locale)
    {
        if (array_key_exists($locale, $this->supported)) {
            session()->set('playground_lang', $locale);
        }
        return redirect()->to(base_url('examples/lang'));
    }
}
