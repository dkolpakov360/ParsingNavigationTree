<?php

class ParsingMenu
{
    public $domain;
    public $deep = 1; 

    public function __construct(string $domain)
    {
       $this->domain = $domain; 
    }

    public function parse(?string $link): array
    {   
        $domain = $this->domain;
        $html = file_get_contents($domain.$link);
        $pattern = '/<a class="catalog-section-list-item-title intec-cl-text-hover" href="(.+?)">(.+?)<\/a>/';

        preg_match_all($pattern, $html, $menu);

        $menu = array_combine($menu[1], $menu[2]);

        asort($menu);

        if (!empty($menu)) {
            foreach ($menu as $link => $name) {
                $menu[$link] = [
                    'name' => $name,
                    'sub_menu' => $this->parse($link),
                ];
            }
        }

        return $menu;
    }

    public function showDeeper($menu): void
    {
        foreach ($menu as $url => $item) {
            $deep_str = str_repeat('-', $this->deep);
            echo "{$deep_str} <a href='{$url}'>{$item['name']}</a><br>";

            if (!empty($item['sub_menu'])) {
                $this->deep++;
                $this->showDeeper($item['sub_menu']);
                $this->deep--;
            }

            if ($this->deep == 1) {
                echo "<hr>";
            }
        }
    }

}

$parsing = new ParsingMenu('https://klinikabudzdorov.ru');
$menu = $parsing->parse('/uslugi/');
$parsing->showDeeper($menu);