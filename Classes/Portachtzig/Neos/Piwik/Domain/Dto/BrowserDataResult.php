<?php
namespace Portachtzig\Neos\Piwik\Domain\Dto;

/*
 * This script belongs to the Neos CMS package "Portachtzig.Neos.Piwik".
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;

class BrowserDataResult implements \JsonSerializable
{

    /**
     * The Piwik response, formatted as a json string
     *
     * @var string
     */
    protected $response;

    /**
     * @param string $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        $results = json_decode($this->response->getContent(), true);
        $totalVisits = 0;
        $clientBrowser = array();
        $allBrowser = array();
        foreach ($results as $year => $devices) {
            if (!empty($devices)) {
                foreach ($devices as $device) {
                    $totalVisits = $totalVisits + $device['nb_visits'];
                }
                foreach ($devices as $device) {
                    $browser = $device['label'];
                    $clientBrowser[$browser] = 0;
                    $clientBrowser[$browser] = $clientBrowser[$browser] + $device['nb_visits'];
                    $allBrowser[] = array(
                        'browsers' => $browser,
                        'uniquePageviews' => $clientBrowser[$browser],
                        'percent' => ($totalVisits == 0 ? 0 : round(($clientBrowser[$browser] * 100 / $totalVisits)))
                    );
                }
            }
        }
        return array(
            'totals' => array('uniquePageviews' => $totalVisits),
            'rows' => $allBrowser
        );
    }

}
