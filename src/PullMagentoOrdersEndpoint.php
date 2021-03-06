<?php
declare(strict_types=1);

namespace ToBigQuery;

use Curl\Curl;
use Exception;

/**
 * This class uses the Magento REST API to retrieve the Orders.
 */
final class PullMagentoOrdersEndpoint
{
    /**
     * @var string
     */
    protected $baseUrl;
    /**
     * @var string
     */
    protected $endpointUrl = '/rest/V1/orders';
    /**
     * @var Curl
     */
    protected $client;

    public function __construct(string $baseUrl, string $accessToken)
    {
        $this->baseUrl = $baseUrl;
        $this->client = new Curl($baseUrl . $this->endpointUrl);
        $this->client->setHeader('Authorization', "Bearer $accessToken");
    }

    /**
     * Perform endpoint request and return Shipments.
     *
     * @param int $fromEntityId
     * @param int $pageSize
     * @param int $currentPage
     * @return array
     * @throws Exception
     */
    public function performRequest(
        int $fromEntityId,
        int $currentPage = 1,
        int $pageSize = 25
    ) {
        $query = [
            'searchCriteria[filter_groups][0][filters][0][field]' => 'entity_id',
            'searchCriteria[filter_groups][0][filters][0][value]' => $fromEntityId,
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'gteq',

            'searchCriteria[currentPage]' => $currentPage,
            'searchCriteria[pageSize]' => $pageSize,
        ];

        $this->client->get($query);
        if ($this->client->error) {
            throw new Exception(
                sprintf('Unsuccessful request: %s', $this->client->errorMessage)
            );
        }

        $raw = json_decode($this->client->getRawResponse(), true);
        return $raw;
    }
}
