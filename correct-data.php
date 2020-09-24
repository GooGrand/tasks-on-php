<?php
require __DIR__ . '/vendor/autoload.php';

// credentials.json помещается в корень проекта

$spreadsheetId = '1emPPR3TyZFDqMLnPk92_nHxUp5vQ8eYpXpukmEnwhsQ'; //ID нужного листа
$range_read = 'Sheet1';
$range_write = 'B1';

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}
function prepareUrl($address)
{
    $parts = preg_split("/[\s,]+/", $address);
    $urlAddress = implode('%20', $parts);
    $url = 'https://www.google.com/search?tbm=map&authuser=0&hl=en&gl=ru&q=' . $urlAddress . '&oq=' . $urlAddress . '&gs_l=maps.12..38i10i428k1j38i443i428k1l4.23689.23689.1.38082.5.5.0.0.0.0.207.207.2-1.5.0....0...1ac.1.64.maps..0.5.1095.3..38i39k1.109.';
    return $url;
}

function getPageByUrl ($url)
{
    $curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    //Выполняем запрос:		
    $result = curl_exec($curl);

	if ($result === false) {
        echo "Ошибка CURL: " . curl_error($curl);
        curl_close($curl);
		return false;
	} else {
        curl_close($curl);
		return $result;
	}
}
function correctAddresses($spreadsheetId, $range_read, $range_write)
{
    $client = getClient();
    $service = new Google_Service_Sheets($client);

    $response = $service->spreadsheets_values->get($spreadsheetId, $range_read);
    $values = $response->getValues();

    $corrected_values = [];
    if (empty($values)) {
        print "No data found.\n";
    } else {
        print "Address \n";
        foreach ($values as $row) {
            // echo gettype($row[0]);     //Комментирую, потому что проще делать дебаг
            $url = prepareUrl($row[0]);
            $json_string = getPageByUrl($url);
            // $json_array = explode(']', $json_string);
            // $address = str_replace($filter_array, '', $json_array[24]);  // Пробовал перебирать массив, но результат зависел от запроса
            $address_with_tail = stristr($json_string, ',[2,[["');
            $address_with_needle = stristr($address_with_tail, ', USA"', true);
            $address = str_replace(',[2,[["', '', $address_with_needle);
            if(!$address_with_needle) {
                $address = 'Non american address';
            }
            if (!$address_with_tail) {
                $address = 'Incorrect address';
            }
            $row_array = [$address];
            array_push($corrected_values, $row_array);
        }
        // echo var_dump($corrected_values);
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $corrected_values
        ]);
        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];
        $result = $service->spreadsheets_values->append($spreadsheetId, $range_write, $body, $params);
        printf("%d cells updated.", $result->getUpdates()->getUpdatedCells());
    }
}

correctAddresses($spreadsheetId, $range_read, $range_write);