<?php

namespace App\Services;

class EnvFileWriter
{
    /**
     * Met à jour (ou ajoute) des clés dans le fichier .env du projet.
     *
     * @param  array<string, string>  $values
     */
    public function update(array $values): void
    {
        $path = base_path('.env');
        $content = file_exists($path) ? file_get_contents($path) : '';
        $lines = $content === '' ? [] : explode("\n", $content);

        foreach ($values as $key => $value) {
            $formatted = $key.'='.$this->formatValue($value);
            $found = false;

            foreach ($lines as $i => $line) {
                if (preg_match('/^'.preg_quote($key, '/').'=/', $line)) {
                    $lines[$i] = $formatted;
                    $found = true;
                    break;
                }
            }

            if (! $found) {
                $lines[] = $formatted;
            }
        }

        file_put_contents($path, implode("\n", $lines));
    }

    private function formatValue(string $value): string
    {
        if ($value === '' || preg_match('/[\s"#]/', $value)) {
            return '"'.str_replace('"', '\\"', $value).'"';
        }

        return $value;
    }
}
