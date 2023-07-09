<?php

namespace App\Services;

use GuzzleHttp\Client;

class MavenDownloader
{

    private string $plugin_name;
    private string $version;

    public function setPluginName(string $plugin_name): self
    {
        $this->plugin_name = $plugin_name;
        return $this;
    }

    public function setPluginVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function download(): string|bool
    {
        try {
            $base_url = config('services.techscode.maven.url');
            $username = config('services.techscode.maven.username');
            $password = config('services.techscode.maven.password');

            $jar_file_name = "$this->plugin_name-$this->version.jar";
            $url = "$base_url/repository/techscode-plugins/me/TechsCode/$this->plugin_name/$this->version/$jar_file_name";

            if (!file_exists(storage_path("app/plugins/"))) {
                mkdir(storage_path("app/plugins"), 0777, true);
            }

            $client = new Client();
            $response = $client->request('GET', $url, [
                'auth' => [$username, $password],
                'sink' => storage_path("app/plugins/$jar_file_name")
            ]);

            if ($response->getStatusCode() !== 200) {
                \Log::error($response->getBody()->getContents());
                return false;
            }

            return $jar_file_name;
        }
        catch (\Exception $e){
            \Log::error($e->getMessage());
            return false;
        }
    }

}
