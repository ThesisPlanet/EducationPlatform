<?php

/**
 * Thesis Planet - Digital Education Platform
 *
 * LICENSE
 *
 * This source file is subject to the licensing terms found at http://www.thesisplanet.com/platform/tos
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to sales@thesisplanet.com so we can send you a copy immediately.
 *
 * @category  ThesisPlanet
 * @copyright  Copyright (c) 2009-2012 Thesis Planet, LLC. All Rights Reserved. (http://www.thesisplanet.com)
 * @license   http://www.thesisplanet.com/platform/tos   ** DUAL LICENSED **  #1 - Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License. #2 - Thesis Planet Commercial Use EULA.
 */
namespace App\Form\Configuration;

class AWS extends \Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        $this->setOptions(
                array(
                        'class' => 'form-stacked'
                ));

        $key = new \Zend_Form_Element_Text('key');
        $key->setLabel('Account Access Key ID')
            ->setDescription(
                "My Account/Security Credentials/Access Keys/Access Key ID")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));

        $secretkey = new \Zend_Form_Element_Text('secretKey');
        $secretkey->setLabel('Secret Access Key')
            ->setDescription(
                "My Account/Security Credentials/Access Keys/Access Key ID -> Show")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => 'span8'
                ));

        $accountId = new \Zend_Form_Element_Text('accountId');
        $accountId->setLabel('Account ID')
            ->setDescription(
                "My Account/My Account - Account Number (xxxx-xxxx-xxxx)")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));

        $canonicalId = new \Zend_Form_Element_Text('canonicalId');
        $canonicalId->setLabel('Canonical ID')
            ->setDescription(
                "My Account/Security Credentials/AccountIdentifiers/Canonical User Id/View canonical user ID (bottom of page)")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => 'span8'
                ));

        $canonicalName = new \Zend_Form_Element_Text('canonicalName');
        $canonicalName->setLabel('Canonical Name')
            ->setDescription("name of the account owner")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));

        $bucket = new \Zend_Form_Element_Text('bucket');
        $bucket->setLabel('AWS Bucket name')
            ->setDescription(
                'AWS Bucket - you may specify a custom bucket name')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(array(
                'class' => 'span8'
        ));

        $CFStreamingDistributionId = new \Zend_Form_Element_Text(
                'CFStreamingDistributionId');
        $CFStreamingDistributionId->setLabel(
                'AWS CloudFront Streaming Distribution ID')
            ->setDescription(
                "AWS Management Console/CloudFront/ID (for the streaming distribution type)")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => 'span8'
                ));

        $CFDownloadDistributionId = new \Zend_Form_Element_Text(
                'CFDownloadDistributionId');
        $CFDownloadDistributionId->setLabel(
                'AWS CloudFront Download Distribution ID')
            ->setDescription(
                "AWS Management Console/CloudFront/ID (for the download distribution type)")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => 'span8'
                ));

        $CFKeypairId = new \Zend_Form_Element_Text('CFKeypairId');
        $CFKeypairId->setLabel('AWS CloudFront Keypair ID')
            ->setDescription(
                "Cloudfront Keypairs are used to sign URLs in order to ensure secure content delivery - My Account/Security Credentials/Key Pairs")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => 'span8'
                ));

        $CFPrivateKeyPEM = new \Zend_Form_Element_Textarea('CFPrivateKeyPEM');
        $CFPrivateKeyPEM->setLabel(
                'AWS CloudFront Private Key PEM (Certificate) File')
            ->setDescription(
                "My Account/Security Credentials/Create a New Key Pair -- Downloaded PEM File (Open with a text editor such as notepad or textmate")
            ->setRequired(true)
            ->setOptions(
                array(
                        'class' => 'span8'
                ));

        $CFCanonicalId = new \Zend_Form_Element_Text('CFCanonicalId');
        $CFCanonicalId->setLabel('AWS CloudFront Canonical ID')
            ->setDescription(
                "AWS Management Console/CloudFront/Navigation/Private Content/Origin Access Identity/Amazon S3 Canonical User ID")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => 'span8'
                ));

        $CFDistributionStreamer = new \Zend_Form_Element_Text(
                'CFDistributionStreamer');
        $CFDistributionStreamer->setLabel(
                "CloudFront Distribution Domain Name or CNAME plus '/cfx/st' for the Streaming distibution. example: 'vme-stream.thesisplanet.com/cfx/st'")
            ->setDescription("Aws Management Console/CloudFront/CNAMEs'")
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => 'span8'
                ));

        $CFDownloadDistributionURL = new \Zend_Form_Element_Text(
                'CFDownloadDistributionURL');
        $CFDownloadDistributionURL->setLabel(
                'CloudFront Distribution Domain Name or CNAME for the download distribution')
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addFilter('HtmlEntities')
            ->setOptions(
                array(
                        'class' => 'span8'
                ));

        $S3Validate = new \Zend_Form_Element_Hidden("s3Validate");
        $S3Validate->addValidator(new \App\Validate\Configuration\AWS\S3())
            ->setValue("run")
            ->setAutoInsertNotEmptyValidator(false)
            ->setRequired(true);

        $CFDValidate = new \Zend_Form_Element_Hidden("cfdValidate");
        $CFDValidate->addValidator(
                new \App\Validate\Configuration\AWS\CFDownload())
            ->setValue("run")
            ->setAutoInsertNotEmptyValidator(false)
            ->setRequired(true);

        $CFSValidate = new \Zend_Form_Element_Hidden("cfsValidate");
        $CFSValidate->addValidator(
                new \App\Validate\Configuration\AWS\CFStreaming())
            ->setValue("run")
            ->setAutoInsertNotEmptyValidator(false)
            ->setRequired(true);

        $submit = new \Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit Changes')->setOptions(
                array(
                        'class' => 'btn btn-primary'
                ));
        // Attach elements to the form
        $this->addElement($S3Validate)
            ->addElement($CFDValidate)
            ->addElement($CFSValidate)
            ->addElement($key)
            ->addElement($bucket)
            ->addElement($secretkey)
            ->addElement($accountId)
            ->addElement($canonicalId)
            ->addElement($canonicalName)
            ->addElement($CFStreamingDistributionId)
            ->addElement($CFDownloadDistributionId)
            ->addElement($CFKeypairId)
            ->addElement($CFPrivateKeyPEM)
            ->addElement($CFCanonicalId)
            ->addElement($CFDistributionStreamer)
            ->addElement($CFDownloadDistributionURL)
            ->addElement($submit);
    }

    public function setSubmitLabel ($label)
    {
        $this->submit->setLabel($label);
    }
}