<?php

namespace Chainstarters;

use GuzzleHttp\Client;
use Chainstarters\Exception\SDKException;

class Chainstarters
{
    private $client;
    private $config;

    protected $getUserWalletQueryString = <<<'GRAPHQL'
    query getUserWallet($input: GetUserWalletInput!) {
      getUserWallet(input: $input)
    }
    GRAPHQL;

    protected $getUserWalletsBatchQuery = <<<'GRAPHQL'
    query getUserWalletsBatch($input: GetUserWalletsBatchInput!) {
      getUserWalletsBatch(input: $input)
    }
    GRAPHQL;

    protected $getErc20BalanceOf = <<<'GRAPHQL'
    query erc20BalanceOf($user_id: String!) {
        erc20BalanceOf(user_id: $user_id)
    }
    GRAPHQL;

    protected $spendERC20Mutation = <<<'GRAPHQL'
    mutation spendERC20($input: SpendERC20Input!) {
        spendERC20(input: $input)
      }
    GRAPHQL;

    protected $adminMintProjectERC20 = <<<'GRAPHQL'
    mutation AdminMintProjectERC20($userId: String, $amount: String) {
        adminMintProjectERC20(user_id: $userId, amount: $amount)
      }
    GRAPHQL;

    public function __construct(array $config)
    {
        $this->validateConfig($config);

        // Assign the config values
        $this->config = $config;

        $baseUri = 'https://cs-' . $this->config['project_id'] . '.prime-jackpot-expanse.chainstarters.io';

        $this->client = new Client([
            'base_uri' => $baseUri,
            'headers' => [
                'Authorization' => 'owner:' . $this->config['owner_api_key'],
                'Content-Type' => 'application/json'
            ]
        ]);
    }
    private function validateConfig(array $config)
    {
        if (!isset($config['project_id'])) {
            throw new SDKException('Project ID is not set in the configuration.');
        }
        if (!isset($config['owner_api_key'])) {
            throw new SDKException('Owner API Key is not set in the configuration.');
        }
    }
    private function send($query, $variables = [])
    {
        try {
            $response = $this->client->post('/prod', [
                'json' => [
                    'query' => ($query),
                    'variables' => $variables
                ]
            ]);

            $body = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                $data = json_decode($body, true);
                return $data['data'];
            } else {
                throw new SDKException($body, 0);
            }
        } catch (\Exception $e) {
            error_log('Error fetching data: ' . $e->getMessage());
            throw new SDKException('Error executing query.', 0, $e);
        }
    }

    public function getUserWallet(string $email)
    {

        try {
            $variables = ['input' => ['email' => $email]];

            // Execute the query
            $response = $this->send(
                $this->getUserWalletQueryString,
                $variables
            );

            // Access the specific part of the response
            return isset($response['getUserWallet']) ? $response['getUserWallet'] : null;
        } catch (\Exception $e) {
            error_log('Error fetching user wallet address: ' . $e->getMessage());
            throw new SDKException('Error fetching user wallet address.', 0, $e);
        }
    }

    public function getUserWalletsBatch(array $emails)
    {

        try {
            $variables = ['input' => ['emails' => $emails]];

            // Execute the query
            $response = $this->send(
                $this->getUserWalletsBatchQuery,
                $variables
            );

            return isset($response['getUserWalletsBatch']) ? $response['getUserWalletsBatch'] : null;
        } catch (\Exception $e) {
            error_log('Error fetching user wallet addresses: ' . $e->getMessage());
            throw new SDKException('Error fetching user wallet addresses.', 0, $e);
        }
    }


    public function getErc20Balance(string $user_id)
    {

        try {
            $variables =  ['user_id' => $user_id];

            // Execute the query
            $response = $this->send(
                $this->getErc20BalanceOf,
                $variables
            );

            // Access the specific part of the response
            return isset($response['erc20BalanceOf']) ? $response['erc20BalanceOf'] : null;
        } catch (\Exception $e) {
            error_log('Error fetching user wallet address: ' . $e->getMessage());
            throw new SDKException('Error fetching user wallet address.', 0, $e);
        }
    }

    public function spendERC20Mutation(string $email, string $amount_in_pennies)
    {

        try {
            $variables = ['input' => ['email' => $email, 'amount_in_pennies' => $amount_in_pennies]];

            // Execute the query
            $response = $this->send(
                $this->spendERC20Mutation,
                $variables
            );

            // Access the specific part of the response
            return isset($response['spendERC20']) ? $response['spendERC20'] : null;
        } catch (\Exception $e) {
            error_log('Error fetching user wallet address: ' . $e->getMessage());
            throw new SDKException('Error fetching user wallet address.', 0, $e);
        }
    }

    public function adminMintProjectERC20(string $email, string $amount_in_pennies)
    {

        try {
            $variables =  ['userId' => $email, 'amount' => $amount_in_pennies];

            // Execute the query
            $response = $this->send(
                $this->adminMintProjectERC20,
                $variables
            );

            // Access the specific part of the response
            return isset($response['adminMintProjectERC20']) ? $response['adminMintProjectERC20'] : null;
        } catch (\Exception $e) {
            error_log('Error fetching user wallet address: ' . $e->getMessage());
            throw new SDKException('Error fetching user wallet address.', 0, $e);
        }
    }
}
