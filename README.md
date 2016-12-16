# Customize-Email-php
A tool in PHP. It allows you to automatically generate customized emails in batch from a template and an excel file.

"template" is the file of the template. "conf" is the excel file of customized information of each email. The column "Address" and "Subject" are required.
The name of other columns should start with "#" and has the corresponding matching in the template.

Example:
>>php -f script.php template="template.txt" conf="conf.xls"

Template:
Hello #recipient,

This is an email to #recipient. You can have you customized text first name #para_1, last name #para_2, and geo-location #para_3 defined for each recipient.

Enjoy it!
Sender

Email sent:
To: jackDaniels@gmail.com
Subject: Test Email
Message:
Hello Jack Daniel's,

This is an email to Jack Daniel's. You can have you customized text first name Jack, last name Daniel, and geo-location 182 Lynchburg Highway Lynchburg, TN 37352 defined for each recipient.

Enjoy it!
Sender
