## Listamester PHP API

Based on https://listamester.hu/docs/pubApiDoc.txt

### Usage

Add to your system the constants from config.php and include the class.listamester.php

```php
$listamester = new Listamester();
// get our groups -> return groups in an array
$listamester->getGroups();
// check member exists in the group -> return true / false
$listamester->memberExists('user@email.com');
// get member datas -> return array
$listamester->getMember('user@email.com');
// subscribe new member -> return true / false
$listamester->subscribe('John Doe', 'user@email.com');
// change default group ID
$listamester->groupID = 12345;
```
