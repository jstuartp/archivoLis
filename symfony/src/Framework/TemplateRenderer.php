<?php
namespace App\Framework;

class TemplateRenderer
{
    public function __construct(private string $templateDir)
    {
    }

    public function render(string $template, array $context = []): string
    {
        $templatePath = $this->templateDir . '/' . ltrim($template, '/');
        if (!is_file($templatePath)) {
            throw new \RuntimeException(sprintf('Template "%s" not found', $template));
        }

        extract($context, EXTR_SKIP);

        ob_start();
        include $templatePath;
        $content = ob_get_clean();

        $baseTemplate = $this->templateDir . '/base.html.php';
        if (is_file($baseTemplate)) {
            ob_start();
            include $baseTemplate;
            return (string) ob_get_clean();
        }

        return (string) $content;
    }
}
