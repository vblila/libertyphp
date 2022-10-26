<?php

namespace Libertyphp\Views;

class Content
{
    public static function render(string $viewPath, array $params = []): string
    {
        extract($params);

        ob_start();
        require($viewPath);
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}
