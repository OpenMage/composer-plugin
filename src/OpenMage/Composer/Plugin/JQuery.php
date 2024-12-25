<?php

declare(strict_types=1);

namespace OpenMage\Composer\Plugin;

/**
 * Class JQuery
 */
class JQuery extends AbstractVendorCopyPlugin
{
    public function getVendorName(): string
    {
        return 'components/jquery';
    }

    public function getCopySource(): string
    {
        return '';
    }


    public function getCopyTarget(): string
    {
        return 'js/lib/jquery';
    }


    public function getFilesByName(): array
    {
        return ['*.map', '*.js'];
    }
}
