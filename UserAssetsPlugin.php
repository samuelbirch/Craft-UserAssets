<?php
/**
 * User Assets plugin for Craft CMS
 *
 * Users have there own isolated asset location within the defined sources
 *
 * @author    madebyjam
 * @copyright Copyright (c) 2017 madebyjam
 * @link      https://madebyjam.com
 * @package   UserAssets
 * @since     1.0.0
 */

namespace Craft;

class UserAssetsPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('User Assets');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Users have there own isolated asset location within the defined sources.');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/samuelbirch/Craft-UserAssets/blob/master/README.md';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://github.com/samuelbirch/Craft-UserAssets/master/releases.json';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'madebyjam';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://madebyjam.com';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return false;
    }

    /**
     */
    public function onBeforeInstall()
    {
    }

    /**
     */
    public function onAfterInstall()
    {
    }

    /**
     */
    public function onBeforeUninstall()
    {
    }

    /**
     */
    public function onAfterUninstall()
    {
    }
    
    /**
	 * @param $sources
	 * @param $context
	 */
	public function modifyAssetSources(&$sources, $context)
	{
		// If the current user is an admin, just let them see everything
		if (craft()->userSession->isAdmin())
		{
			return;
		}
		
		$username = craft()->userSession->getUser()->username;
		
		$incomingSources = $sources;
		$i = 0;
		
		foreach($incomingSources as $key => $source){
			
			$parentFolderId = substr($key, 7);
			
			if(!craft()->assets->findFolder(['name'=>$username, 'sourceId'=>$parentFolderId])){
				$folder = craft()->assets->createFolder($parentFolderId, $username);
				$folderId = $folder->getDataItem('folderId');
				
				$newFolder = ['folder:'.$folderId => [
					'label' => $source['label'],
					'hasThumbs' => $source['hasThumbs'],
					'criteria' => ['folderId'=>$folderId],
					'data' => $source['data'],
					'nested' => [],
				]];
				
				$sources = array_slice($sources, 0, $i, true) +
					$newFolder +
					array_slice($sources, $i + 1, null, true);
				
			}else{
				
				foreach($source['nested'] as $subKey => $subSource){
					if($subSource['label'] == $username){
						$subSource['label'] = $source['label'];
						$sources = array_slice($sources, 0, $i, true) +
							[$subKey => $subSource] +
							array_slice($sources, $i + 1, null, true);
					}
				}
			}

			$i++;
		}
		
	}

}