<?php

declare(strict_types=1);

namespace App\Twig\Components\Modal;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * @author  Markus Bierau <markus.bierau@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
#[AsTwigComponent()]
class ConfirmModalLink
{
    public string $type = 'link';
    public string $modalTitle;
    public string $modalContent;
    public string $linkUrl;
    public bool $isAjax = false;
    public ?string $classes = null;
    public string $declineText = 'form.common.cancel';
    public string $submitText = 'form.common.save';
}
