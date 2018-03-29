<?php

namespace App\Control;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HTMLEditor\HtmlEditorField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\SiteConfig\SiteConfig;

class SiteSettingsAdmin extends LeftAndMain implements PermissionProvider
{
    private static $url_segment = 'site-settings';

    private static $url_rule = '/$Action/$ID/$OtherID';

    private static $menu_title = 'Site Settings';

    private static $menu_priority = -0.5;

    private static $menu_icon = 'app/images/site-settings.png';

    private static $required_permission_codes = ['EDIT_SITE_SETTINGS'];
    
    public function providePermissions()
    {
        return [
            'EDIT_SITE_SETTINGS' => [
                'name' => _t('CMSMain.ACCESS', "Access to '{title}' section", ['title' => 'Site Settings']),
                'category' => _t('Permission.CMS_ACCESS_CATEGORY', 'CMS Access')
            ]
        ];
    }

    public function getEditForm($id = null, $fields = null)
    {
        $config = SiteConfig::current_site_config();
        $fields = FieldList::create(
            TabSet::create("SiteSettings",
                TabSet::create("Root",
                    Tab::create("Main",
                        EmailField::create('EmailAddress')
                    )
                )
            )
        );

        $actions = FieldList::create(
            FormAction::create('saveSettings', _t('CMSMain.SAVE', 'Save'))
                ->setUseButtonTag(true)
                ->addExtraClass('btn btn-primary font-icon-save')
                ->setAttribute('data-icon', 'accept')
        );

        $form = Form::create($this, 'EditForm', $fields, $actions)
            ->setHTMLID('Form_EditForm')
            ->addExtraClass('cms-content center cms-edit-form')
            ->setAttribute('data-pjax-fragment', 'CurrentForm')
            ->loadDataFrom($config)
            ->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

        $this->extend('updateEditForm', $form);

        return $form;
    }

    /**
     * @param array $data
     * @param Form $form
     * @param HTTPRequest $request
     * @return SilverStripe\Control\HTTPResponse
     */
    public function saveSettings(array $data, Form $form, HTTPRequest $request)
    {
        $config = SiteConfig::current_site_config();
        $form->saveInto($config);

        try {
            $config->write();
        } catch(ValidationException $ex) {
            $form->sessionMessage($ex->getResult()->message(), 'bad');
            return $this->getResponseNegotiator()->respond($this->request);
        }

        $this->response->addHeader('X-Status', rawurlencode(_t('LeftAndMain.SAVEDUP', 'Saved.')));
        return $this->getResponseNegotiator()->respond($this->request);
    }
}
