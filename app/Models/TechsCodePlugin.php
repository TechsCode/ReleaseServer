<?php

namespace App\Models;

class TechsCodePlugin
{
    public static array $valid_plugins = [
        "ultrapermissions" => "UltraPermissions",
        "ultraeconomy" => "UltraEconomy",
        "ultramotd" => "UltraMotd",
        "ultrapunishments" => "UltraPunishments",
        "ultraregions" => "UltraRegions",
        "ultracustomizer" => "UltraCustomizer",
        "ultrascoreboards" => "UltraScoreboards",
        "insanevaults" => "InsaneVaults",
        "insaneshops" => "InsaneShops",
        "insaneannouncer" => "InsaneAnnouncer",
        "techseditor" => "TechsEditor",
        "ultraeconomytest" => "UltraEconomyTest",
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
