<?php


namespace OSN\Framework\DataTypes;


class _String implements DataTypeInterface
{
    private string $data;

    public function __construct(string $string = '')
    {
        $this->data = $string;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->data;
    }

    /**
     * @param $value
     * @return void
     */
    public function set($value)
    {
        $this->data = (string) $value;
    }

    /**
     * The helper methods.
     */

    public function len(bool $countWhiteSpaces = true): int
    {
        return $countWhiteSpaces ? strlen($this->data) : strlen(str_replace(' ', '', $this->data));
    }

    public function substr(int $from, int $to = -1)
    {
        if ($to === -1)
            $to = strlen($this->data);

        return substr($this->data, $from, $to);
    }

    public function ltrim(): string
    {
        return ltrim($this->data);
    }

    public function rtrim(): string
    {
        return rtrim($this->data);
    }

    public function trim(): string
    {
        return trim($this->data);
    }

    public function escape(): string
    {
        $str = $this->data;

        if (preg_match_all('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->data,$matches)) {
            if (!empty($matches[0])){
                $replacements = implode($matches[0]);
                $str = addcslashes($str, $replacements);
            }
        }

        return $str;
    }

    public function specialChars(): string
    {
        return htmlspecialchars($this->data);
    }

    public function specialCharsDecode(): string
    {
        return htmlspecialchars_decode($this->data);
    }

    public function entities(int $params = ENT_COMPAT, ?string $encoding = null, bool $double_encoding = true): string
    {
        return htmlentities($this->data, $params, $encoding, $double_encoding);
    }

    public function entitiesDecode(int $params = ENT_COMPAT, ?string $encoding = null): string
    {
        return html_entity_decode($this->data, $params, $encoding);
    }

    public function parseInt(): int
    {
        return (int) $this->data;
    }

    public function parseFloat(): int
    {
        return (float) $this->data;
    }

    public function parseDouble(): int
    {
        return (double) $this->data;
    }

    public function random(int $len = 16, string$chars = null): string
    {
        $chars = str_split($chars ?? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_');
        $random = '';

        for ($i = 0; $i < $len; $i++) {
            $rand = array_rand($chars, 1);
            $random .= $chars[$rand];
        }

        return $random;
    }

    public function toJSON()
    {
        return json_encode($this->data);
    }

    public function parseJSON()
    {
        return json_decode($this->data);
    }

    public function slug(string $delim = '-')
    {
        $delims = "\\\n\r~`!@#\$%^&*()_+=\"\';:,[]{}?<>";
        return strtolower(preg_replace("/( )+/", $delim, trim(preg_replace("/[(\-+) \\(\/)\n\?\<\>\r\~\`\!\@\#\$\%\^\&\*\(\)\_\+\=\"\'\;\:\,\[\]\{\}]/", ' ', $this->data))));
    }

    public function test(string $regex): bool
    {
        return preg_match($regex, $this->data);
    }

    public function match(string $regex, &$matches = null, int $flags = 0, int $offset = 0)
    {
        return preg_match($regex, $this->data, $matches, $flags, $offset);
    }

    public function replace($regex, $replacement, int $limit = -1, &$count = null)
    {
        return preg_replace($regex, $replacement, $this->data, $limit,$count);
    }

    public function isURL()
    {
        return filter_var($this->data, FILTER_VALIDATE_URL);
    }
}
