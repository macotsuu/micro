<?php
    namespace Micro\Template;

    class Template
    {
        private array $blocks = [];

        private string $viewPath;
        private string $cachePath;
        private bool $enableCache = false;

        public function __construct(string $viewPath, string $cachePath = null)
        {
            $this->viewPath = $viewPath;

            if (!is_null($cachePath)) {
                $this->enableCache = true;
                $this->cachePath = $cachePath;

            }
        }

        public function render(string $tmpl, $params = []): string
        {
            $file = $this->enableCache ? $this->cache($tmpl) : $this->view($tmpl);

            ob_start();

            extract($params, EXTR_SKIP);
            require $file;

            return ob_get_clean();
        }

        private function view(string $tmpl): string
        {
            return $this->viewPath . $tmpl;
        }

        private function cache(string $tmpl): string
        {
            if (!file_exists($this->cachePath)) {
                mkdir($this->cachePath, 0744, true);
            }

            $cachedFile = $this->cachePath . str_replace(['/', '.html'], ['_', ''], $tmpl . '.php');
            if (!$this->enableCache || !file_exists($cachedFile) || filemtime($cachedFile) < filemtime($this->viewPath . $tmpl)) {
                $code = $this->includeFiles($tmpl);
                $code = $this->compile($code);

                file_put_contents($cachedFile, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
            }

            return $cachedFile;
        }

        private function compile(string $code): string
        {
            $code = $this->compileBlocks($code);
            $code = $this->compileYield($code);
            $code = $this->compileEscapedEchos($code);
            $code = $this->compileEchos($code);

            return $this->compilePHP($code);
        }

        private function includeFiles(string $file): string
        {
            $code = file_get_contents($this->viewPath . $file);

            preg_match_all('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', $code, $matches, PREG_SET_ORDER);
            foreach ($matches as $value) {
                $code = str_replace($value[0], $this->includeFiles($value[2]), $code);
            }

            return preg_replace('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', '', $code);
        }

        private function compileBlocks(string $code): string
        {

            preg_match_all('/{% ?block ?(.*?) ?%}(.*?){% ?endblock ?%}/is', $code, $matches, PREG_SET_ORDER);
            foreach ($matches as $value) {
                if (!array_key_exists($value[1], $this->blocks)) $this->blocks[$value[1]] = '';
                if (!str_contains($value[2], '@parent')) {
                    $this->blocks[$value[1]] = $value[2];
                } else {
                    $this->blocks[$value[1]] = str_replace('@parent', $this->blocks[$value[1]], $value[2]);
                }
                $code = str_replace($value[0], '', $code);
            }
            return $code;
        }

        private function compileYield(string $code): string
        {
            foreach($this->blocks as $block => $value) {
                $code = preg_replace('/{% ?yield ?' . $block . ' ?%}/', $value, $code);
            }

            return preg_replace('/{% ?yield ?(.*?) ?%}/i', '', $code);
        }

        private function compilePHP(string $code): string
        {
            return preg_replace('~{%\s*(.+?)\s*%}~is', '<?php $1 ?>', $code);
        }

        private function compileEchos(string $code): string
        {
            return preg_replace('~{{\s*(.+?)\s*}}~is', '<?php echo $1 ?>', $code);
        }

        private function compileEscapedEchos(string $code) : string
        {
            return preg_replace('~{{{\s*(.+?)\s*}}}~is', '<?php echo htmlentities($1, ENT_QUOTES, \'UTF-8\') ?>', $code);
        }
    }