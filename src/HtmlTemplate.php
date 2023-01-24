<?php

class HtmlTemplate
{
    const WEBSITE_NAME = "Servicios DIVTIC";
    const WEBSITE_ICON = "/res/img/logo.svg";

    private DOMDocument $document;
    private DOMElement $root;
    private DOMElement $head;
    private DOMElement $nav;
    private DOMElement $main;
    private string $title;

    public function __construct(string $page_title = "")
    {
        $this->document = new DOMDocument;
        $this->document->formatOutput = true;
        $this->root = $this->document->createElement("html");
        $this->root->setAttribute('lang', 'es');
        $this->root->setAttribute('data-bs-theme', 'dark');
        $this->title = $page_title;

        $this->__constructHead();
        $this->__constructBody();
    }

    private function __constructHead()
    {
        $headElement = $this->document->createElement("head");

        $commentMetaTags = $this->document->createComment("Meta etiquetas");
        $headElement->appendChild($commentMetaTags);

        $metaCharset = $this->document->createElement("meta");
        $metaCharset->setAttribute("charset", "utf-8");
        $headElement->appendChild($metaCharset);

        $title = $this->document->createElement(
            "title",
            $this->title($this->title)
        );
        $headElement->appendChild($title);

        $metaFavicon = $this->document->createElement("meta");
        $metaFavicon->setAttribute("property", "og:image");
        $metaFavicon->setAttribute("content", "/res/img/favicon.png");
        $headElement->appendChild($metaFavicon);

        $metaViewport = $this->document->createElement("meta");
        $metaViewport->setAttribute("name", "viewport");
        $metaViewport->setAttribute("content", "width=device-width, initial-scale=1, shrink-to-fit=no");
        $headElement->appendChild($metaViewport);

        $metaRobots = $this->document->createElement("meta");
        $metaRobots->setAttribute("name", "robots");
        $metaRobots->setAttribute("content", "index,nofollow");
        $headElement->appendChild($metaRobots);

        $commentCss = $this->document->createComment("Hojas de estilo en cascada");
        $headElement->appendChild($commentCss);

        $linkBootstrap = $this->document->createElement("link");
        $linkBootstrap->setAttribute("rel", "stylesheet");
        $linkBootstrap->setAttribute("href", "/res/css/bootstrap.min.css");
        $headElement->appendChild($linkBootstrap);

        $linkFontawesome = $this->document->createElement("link");
        $linkFontawesome->setAttribute("rel", "stylesheet");
        $linkFontawesome->setAttribute("href", "/res/css/fa.min.css");
        $headElement->appendChild($linkFontawesome);

        $linkMainCss = $this->document->createElement("link");
        $linkMainCss->setAttribute("rel", "stylesheet");
        $linkMainCss->setAttribute("href", "/res/css/main.css");
        $headElement->appendChild($linkMainCss);

        $commentCss = $this->document->createComment("Scripts");
        $headElement->appendChild($commentCss);

        $linkBootstrap = $this->document->createElement("script");
        $linkBootstrap->setAttribute("src", "/res/js/bootstrap.bundle.min.js");
        $headElement->appendChild($linkBootstrap);

        $linkJquery = $this->document->createElement("script");
        $linkJquery->setAttribute("src", "/res/js/jquery.min.js");
        $headElement->appendChild($linkJquery);

        $linkScriptMain = $this->document->createElement("script");
        $linkScriptMain->setAttribute("src", "/res/js/main.js");
        $headElement->appendChild($linkScriptMain);

        $this->head = $headElement;
        $this->root->appendChild($this->head);
    }

    private function __constructBody()
    {
        $bodyElement = $this->document->createElement("body");

        $this->nav = $this->__constructNav();
        $bodyElement->appendChild($this->nav);

        $this->main = $this->document->createElement("main");
        $this->main->setAttribute("class", "container pt-3");
        $bodyElement->appendChild($this->main);

        $bodyElement->appendChild($this->__constructFooter());

        $this->root->appendChild($bodyElement);
    }

    private function __constructNav()
    {
        $elementos = [
            [
                'type' => 'link',
                'label' => 'Inicio',
                'active' => true,
                'value' => '/',
            ],
            [
                'type' => 'link',
                'label' => 'Grupos de WhatsApp',
                'value' => '/grupos/',
            ]
        ];

        $navElement = crearElementoDOM($this->document, 'nav', null, [
            "class" => "navbar bg-light-subtle sticky-top navbar-expand-lg"
        ]);

        $containerElement = $this->document->createElement("div");
        $containerElement->setAttribute("class", "container-fluid d-block d-lg-flex");
        $navElement->appendChild($containerElement);

        $navTogglerElement = crearElementoDOM($this->document, "button", null, [
            "type" => "button",
            "class" => "navbar-toggler",
            "data-bs-toggle" => "offcanvas",
            "data-bs-target" => "#offcanvasNavbar",
            "aria-controls" => "offcanvasNavbar",
        ], $containerElement);

        $togglerIconElement = $this->document->createElement("i");
        $togglerIconElement->setAttribute("class", "far fa-bars");
        $navTogglerElement->appendChild($togglerIconElement);

        $navBrandElement = $this->document->createElement("a");
        $navBrandElement->setAttribute("class", "navbar-brand");
        $navBrandElement->setAttribute("href", "/");
        $containerElement->appendChild($navBrandElement);

        $brandIconElement = $this->document->createElement("img");
        $brandIconElement->setAttribute("class", "d-lg-inline-block d-none align-text-top");
        $brandIconElement->setAttribute("src", self::WEBSITE_ICON);
        $brandIconElement->setAttribute("width", "24vw");
        $brandIconElement->setAttribute("height", "24vw");
        $navBrandElement->appendChild($brandIconElement);

        $navBrandElement->appendChild(new DOMText(" " . self::WEBSITE_NAME));

        $offcanvasElement = $this->document->createElement("div");
        $offcanvasElement->setAttribute("class", "offcanvas offcanvas-start");
        $offcanvasElement->setAttribute("style", "width: 18rem;");
        $offcanvasElement->setAttribute("tabindex", "-1");
        $offcanvasElement->setAttribute("id", "offcanvasNavbar");
        $offcanvasElement->setAttribute("aria-labelledby", "offcanvasNavbarLabel");
        $containerElement->appendChild($offcanvasElement);

        $offcanvasHeadElement = $this->document->createElement("div");
        $offcanvasHeadElement->setAttribute("class", "offcanvas-header pb-5 offcanvas-background");
        $offcanvasElement->appendChild($offcanvasHeadElement);

        $offcanvasTitleElement = $this->document->createElement("h5");
        $offcanvasTitleElement->setAttribute("class", "offcanvas-title text-dark");
        $offcanvasTitleElement->setAttribute("id", "offcanvasNavbarLabel");
        $offcanvasHeadElement->appendChild($offcanvasTitleElement);

        $offcanvasHeadIconElement = $this->document->createElement("img");
        $offcanvasHeadIconElement->setAttribute("class", "d-inline-block align-text-top");
        $offcanvasHeadIconElement->setAttribute("src", self::WEBSITE_ICON);
        $offcanvasHeadIconElement->setAttribute("width", "24vw");
        $offcanvasHeadIconElement->setAttribute("height", "24vw");
        $offcanvasTitleElement->appendChild($offcanvasHeadIconElement);
        $offcanvasTitleElement->appendChild(new DOMText(" " . self::WEBSITE_NAME));

        $offcanvasCloseElement = $this->document->createElement("button");
        $offcanvasCloseElement->setAttribute("type", "button");
        $offcanvasCloseElement->setAttribute("class", "btn btn-success text-white");
        $offcanvasCloseElement->setAttribute("data-bs-dismiss", "offcanvas");
        $offcanvasCloseElement->setAttribute("aria-label", "Cerrar");
        $offcanvasHeadElement->appendChild($offcanvasCloseElement);

        $offcanvasCloseIconElement = $this->document->createElement("i");
        $offcanvasCloseIconElement->setAttribute("class", "far fa-times");
        $offcanvasCloseElement->appendChild($offcanvasCloseIconElement);

        $offcanvasBodyElement = $this->document->createElement("div");
        $offcanvasBodyElement->setAttribute("class", "offcanvas-body");
        $offcanvasElement->appendChild($offcanvasBodyElement);

        $navListElement = $this->document->createElement("ul");
        $navListElement->setAttribute("class", "navbar-nav justify-content-start flex-grow-1 pe-3");
        $offcanvasBodyElement->appendChild($navListElement);

        foreach ($elementos as $e) {
            if ($e['type'] == 'link') {
                $navliElement = $this->document->createElement("li");
                $navliElement->setAttribute("class", "nav-item");
                $navListElement->appendChild($navliElement);

                $linkClasses = "nav-link";
                $linkClasses .= array_key_exists('active', $e) && $e['active'] ? ' active' : '';

                $linkElement = $this->document->createElement("a", $e['label']);
                $linkElement->setAttribute("href", $e['value']);
                $linkElement->setAttribute("class", $linkClasses);
                $navliElement->appendChild($linkElement);
            }
        }

        return $navElement;
    }

    private function __constructFooter()
    {
        $creadores = [
            'lordfriky' => 'Daniel Hernández M.',
            'SistemaRayoXP' => 'Edson Armando',
        ];

        $footerElement = $this->document->createElement("footer");
        $footerElement->setAttribute('class', 'text-center pt-5');

        $paragraphElement = $this->document->createElement("p", "Sitio creado por:");
        $paragraphElement->setAttribute('class', 'mb-1');
        $footerElement->appendChild($paragraphElement);

        foreach ($creadores as $nickname => $nombre) {
            $pAuthorElement = $this->document->createElement("div");
            $paragraphElement->appendChild($pAuthorElement);

            $pAuthorElement->appendChild(new DOMText(sprintf("%s (", $nombre)));

            $linkElement = $this->document->createElement("a", $nickname);
            $linkElement->setAttribute("href", sprintf("https://github.com/%s", $nickname));
            $pAuthorElement->appendChild($linkElement);

            $pAuthorElement->appendChild(new DOMText(")"));
        }

        return $footerElement;
    }

    public function title(string $title = "")
    {
        if (!empty($title)) {
            $formatted_title = sprintf("%s | %s", $title, self::WEBSITE_NAME);
        } else {
            $formatted_title = self::WEBSITE_NAME;
        }

        return $formatted_title;
    }

    public function addHeadElement(DOMElement $element)
    {
        $this->head->appendChild($element);
    }

    public function importNodeToContent(DOMElement $element)
    {
        $this->document->importNode($element);
        $this->main->appendChild($element);
    }

    public function getMainElement()
    {
        return $this->main;
    }

    public function getDOMDocument()
    {
        return $this->document;
    }

    public function getHTML()
    {
        return $this->saveHTML();
    }

    public function saveHTML()
    {
        $htmlString = "<!DOCTYPE html>\n";
        $htmlString .= $this->document->saveHTML($this->root);
        return $htmlString;
    }

    public function printHTML()
    {
        echo $this->saveHTML();
    }
}
