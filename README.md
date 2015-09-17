���������
============================

������������
-------------

### ���� ������

������������� � `config/db.php`:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```
����� ����������� ���������� � Instagram, ���������� �������� ���������� � ������� config/base.php :

### config/base.php
```php
        ...
        'instagram' => [
            'class' => 'app\api\components\InstagramHelper',
            'className' => 'MetzWeb\Instagram\Instagram',
            'config' => [
                'apiKey' => 'apiKey',
                'apiSecret' => 'apiSecret',
                'apiCallback' => 'http://test.loc?r=site%2Foauthcallback'
            ]
        ],
```

��� �������� ������ � �� ���������� ��������� �������� "yii migrate/up"

��� ���������� ������������ ����� �������������� ������� (���������� ���� ��������)
'https://api.instagram.com/oauth/authorize?client_id=apiKey&redirect_uri=http%3A%2F%2Ftest.loc%3Fr%3Dsite%252Foauthcallback&scope=basic&response_type=code'

��� ����� ���������� ������ � ������������ �� ���� ���������� ��������� �������  "yii get-count-likes-per-day" � "yii get-count-comments-per-day"

�������� �����
--------------

http://test.loc/api/v1/instagram/media/grab?account=username
http://test.loc/api/v1/instagram/media/media_id?account=username,username2
http://test.loc/api/v1/instagram/media/media?account=username&groupByDay=1
http://test.loc/api/v1/instagram/media/media?account=username&limit=10&offset=20

