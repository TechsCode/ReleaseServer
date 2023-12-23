<?php

namespace App\Models;

class TechsCodePlugin
{
    public static array $valid_plugins = [
        "ultrapermissions"  => "Ultra Permissions",
        "ultrascoreboards"  => "Ultra Scoreboards",
        "ultrapunishments"  => "Ultra Punishments",
        "ultracustomizer"   => "Ultra Customizer",
        "ultraeconomy"      => "Ultra Economy",
        "ultraregions"      => "Ultra Regions",
        "ultramotd"         => "Ultra Motd",
        "insaneshops"       => "Insane Shops",
        "insanespawners"    => "Insane Spawners",
        "insanevaults"      => "Insane Vaults",
        "insaneannouncer"   => "Insane Announcer",
        "insanechatcolors"  => "Insane Chat Colors",
        "techseditor"       => "Techs Editor",
        "ultraeconomytest"  => "Ultra Economy Test",
    ];

    private string $value;

    public function __construct(string|null $plugin_name)
    {
        $plugin_name = str_replace(' ', '', $plugin_name);
        $plugin_name = preg_replace('/[^A-Za-z0-9\-]/', '', $plugin_name);
        $this->value = strtolower($plugin_name);
        return $this;
    }

    public function isValidPlugin(): bool
    {
        return array_key_exists($this->value, self::$valid_plugins);
    }

    public function getValue(): string | bool
    {
        if (!$this->isValidPlugin()) return false;
        return $this->value;
    }

    public function getName(): string | bool
    {
        if (!$this->isValidPlugin()) return false;
        return self::$valid_plugins[$this->value];
    }

    public static function getValidPluginKeys(): array
    {
        return array_keys(self::$valid_plugins);
    }

}
