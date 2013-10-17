Authentication plugin restricted by IP
===

This authentication plugin helps to manage manual accounts being accessed only
by the list of restricted IPs.


Installation
---

Install it as usual:
* Download it (via zip or git) into MOODLE/auth/ip
* Log in into Moodle
* Go to "Notifications"
* Set up the list of IPs enabled to access to your Moodle.
* Save changes.
* Go to Administration->Plugins->Authentication->Manage plugins
* Enable plugin "Authentication by IP".


Usage
---

Setting this authentication type to a user:
* Go to a user profile.
* Set the authentication type "Authentication by IP"

Updating the list of restricted IPs:
* Go to Administration->Plugins->Authentication->Manage plugins
* Update the list of IPs

NOTE: After updating the list of IPs, an email will be sent to the administrator email,
just for security.


License
---

It is released as GPL v3.

Authors: 

* Robert Boloc <robert.boloc@urv.cat>
* Jordi Pujol-Ahulló <jordi.pujol@urv.cat>

Copyright 2013 onwards Servei de Recursos Educatius (http://www.sre.urv.cat)

