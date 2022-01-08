<?php


namespace OSN\Framework\Core;

use OSN\Framework\Exceptions\FileNotFoundException;
use OSN\Framework\Facades\Request;
use OSN\Framework\PowerParser\PowerParser;

class View
{
    protected string $name;
    protected $layout;
    protected array $data;
    protected PowerParser $parser;
    protected string $file;
    protected $title = '';

    /**
     * View constructor.
     * @throws FileNotFoundException
     */
    public function __construct(string $name, $data = [], $layout = '')
    {
        $this->name = str_replace('.', '/', $name);
        $this->layout = $layout === '' ? App::$app->config["layout"] : $layout;
        //$this->layout = $layout === null ? null : $layout;
        $this->data = $data;

        $file = App::$app->config["root_dir"] . "/resources/views/" . $this->name . ".php";

        if (!is_file($file)) {
            $file = App::$app->config["root_dir"] . "/resources/views/" . $this->name . ".power.php";
        }

        if (!is_file($file)) {
            throw new FileNotFoundException("Couldn't find the specified view '{$this->name}': No such file or directory");
        }

        $this->file = $file;
        $this->parser = new PowerParser($file);
    }

    public function render()
    {
        $_names = [];
        $_sections = [];
        $_names_modified = [];
        $_layout = $this->layout;

        $view = $this->renderView($_names, $_sections, $_names_modified, $_layout);

        if ($this->layout === false || $this->layout === null)
             return $view;

        $layout = new Layout($_layout, $this->title, [
            'sections' => $_sections,
            'names' => $_names,
            'names_modified' => $_names_modified,
        ]);

        return preg_replace("/\[\[( *)view( *)\]\]/", $view, $layout);
    }

    public function renderView(&$_names = [], &$_sections = [], &$_names_modified = [], &$_layout = [])
    {
        foreach ($this->data as $key => $value) {
            $$key = $value;
        }

        if (preg_match('/\.power\.php/', basename($this->file))) {
            $isPower = true;
            $file = ($this->parser)()['file'];
        }
        else
            $file = $this->file;

        ob_start();
        include $file;
        $out = ob_get_clean();

        if (isset($isPower))
            unlink($file);

        $this->title = $title ?? '';

        return $out;
    }

    public function getURI()
    {
        return App::request()->baseURI;
    }

    public function __invoke()
    {
        return $this->render();
    }

    public function __toString()
    {
        return $this->render();
    }
}
