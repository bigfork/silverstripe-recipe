<?php

namespace App\Admin;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\HTTPResponse_Exception;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ValidationException;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Versioned\RecursivePublishable;

class SiteSettingsAdmin extends LeftAndMain implements PermissionProvider
{
    private static $url_segment = 'site-settings';

    private static $url_rule = '/$Action/$ID/$OtherID';

    private static $menu_title = 'Site Settings';

    private static $menu_priority = -0.5;

    private static $menu_icon_class = 'font-icon-monitor';

    private static $required_permission_codes = ['EDIT_SITE_SETTINGS'];

    public function providePermissions()
    {
        return [
            'EDIT_SITE_SETTINGS' => [
                'name' => _t(
                    'SilverStripe\\CMS\\Controllers\\CMSMain.ACCESS',
                    "Access to '{title}' section",
                    ['title' => 'Site Settings']
                ),
                'category' => _t('SilverStripe\\Security\\Permission.CMS_ACCESS_CATEGORY', 'CMS Access')
            ]
        ];
    }

    public function getEditForm($id = null, $fields = null)
    {
        $config = SiteConfig::current_site_config();
        $fields = FieldList::create(
            TabSet::create(
                'Root',
                Tab::create(
                    'Main',
                    EmailField::create('EmailAddress', 'Email address'),
                    TextField::create('Telephone', 'Telephone number')
                )
            )
        );

        $actions = FieldList::create(
            FormAction::create('saveSettings', _t('CMSMain.SAVE', 'Save'))
                ->setUseButtonTag(true)
                ->addExtraClass('btn btn-primary font-icon-save')
                ->setAttribute('data-icon', 'accept')
        );

        $form = Form::create(
            $this,
            'EditForm',
            $fields,
            $actions
        )->setHTMLID('Form_EditForm');

        $negotiator = $this->getResponseNegotiator();
        $form->setValidationResponseCallback(function (ValidationResult $errors) use ($negotiator, $form) {
            $request = $this->getRequest();
            if ($request->isAjax() && $negotiator) {
                $result = $form->forTemplate();
                return $negotiator->respond($request, [
                    'CurrentForm' => function () use ($result) {
                        return $result;
                    }
                ]);
            }
            return null;
        });

        $form->addExtraClass('cms-edit-form');
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));
        $form->addExtraClass('flexbox-area-grow fill-height cms-content cms-edit-form');
        $form->setAttribute('data-pjax-fragment', 'CurrentForm');
        $form->loadDataFrom($config);

        if ($form->Fields()->hasTabSet()) {
            $form->Fields()->findOrMakeTab('Root')->setTemplate('SilverStripe\\Forms\\CMSTabSet');
        }

        $this->extend('updateEditForm', $form);

        return $form;
    }

    /**
     * @param array $data
     * @param Form $form
     * @param HTTPRequest $request
     * @return HTTPResponse
     * @throws HTTPResponse_Exception
     */
    public function saveSettings(array $data, Form $form, HTTPRequest $request)
    {
        $config = SiteConfig::current_site_config();
        $form->saveInto($config);

        try {
            $config->write();
            if ($config->hasExtension(RecursivePublishable::class)) {
                /** @var RecursivePublishable $config */
                $config->publishRecursive();
            }
        } catch (ValidationException $ex) {
            $form->setSessionValidationResult($ex->getResult());
            return $this->getResponseNegotiator()->respond($this->request);
        }

        $this->response->addHeader('X-Status', rawurlencode(_t('LeftAndMain.SAVEDUP', 'Saved.')));
        return $this->getResponseNegotiator()->respond($this->request);
    }
}
