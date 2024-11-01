<?php

use Ticketack\WP\TKTApp;
use Ticketack\WP\Helpers\SyncPeopleHelper;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;

class TKT_PEOPLE_COMMANDS extends WP_CLI_Command {
    function import()
    {
        $default_lang = TKTApp::get_instance()->get_config('i18n.default_lang', 'fr');

        if ($default_lang === 'fr') {
            switch_to_locale('fr_FR');
        }

        $people = SyncPeopleHelper::fetch_people();

        $people_ids = array_map(function ($node) { return (string)$node[0]; }, $people->xpath('person/id'));

        foreach ($people_ids as $index => $person_id) {
            WP_CLI::line('Fetching details for person ' . ($index + 1) . ' => #' . $person_id);
            $details = SyncPeopleHelper::fetch_person_details($person_id);
            WP_CLI::line('Details fetched...');

            if (SyncPeopleHelper::must_be_imported($details)) {
                if (SyncPeopleHelper::import($details, $default_lang)) {
                    WP_CLI::success('person #' . $person_id . ' successfully imported!');
                }
            }
        }

        WP_CLI::success('Import is done!');
    }

    function async_import()
    {
        $default_lang = TKTApp::get_instance()->get_config('i18n.default_lang', 'fr');

        if ($default_lang === 'fr') {
            switch_to_locale('fr_FR');
        }

        $people     = SyncPeopleHelper::fetch_people();
        $people_ids = array_map(function ($node) { return (string)$node[0]; }, $people->xpath('person/id'));

        $client   = new Client();
        $requests = [];

        $requests = function ($people_ids) use ($client) {
            foreach ($people_ids as $index => $person_id) {
                WP_CLI::line('Fetching details for person ' . ($index + 1) . ' => #' . $person_id);
                yield function () use ($client, $person_id) {
                    return $client->getAsync(SyncPeopleHelper::details_url($person_id));
                };
            }
        };

        $pool = new Pool($client, $requests($people_ids), [
            'concurrency' => 20,
            'fulfilled' => function ($response, $index) use ($default_lang) {
                $body = $response->getBody();
                $details = SyncPeopleHelper::fetch_person_details_from_xml($body);

                WP_CLI::line('Details fetched for person ' . ($index + 1) . ' ...');
                if (SyncPeopleHelper::must_be_imported($details)) {
                    if (SyncPeopleHelper::import($details, $default_lang)) {
                        WP_CLI::success('person ' . ($index + 1) . ' successfully imported!');
                    }
                }
            },
            'rejected' => function ($reason, $index) {
                WP_CLI::error('person ' . ($index + 1) . ' could not been imported!');
            },
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        // Force the pool of requests to complete.
        $promise->wait();

        WP_CLI::success('Import is done!');
    }
}
WP_CLI::add_command('ticketack:people', 'TKT_PEOPLE_COMMANDS');
