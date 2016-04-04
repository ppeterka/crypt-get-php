# Crypt-get-PHP

Will be possibly the simplest backup for PHP (if it ever gets finished)... 

It ain't fancy, just simple: the less functionality, the less security holes. Having such an app on your server could be an instant backdoor - so use this cautiously. I'm definitely not a security expert, just trying my best to apply common sense as much as possible.

##Advantages
 * No admin interface!
 * NoSQL! Hahaha, just joking, actually there is no database required... Had to put in something webscale.
 * Priceless! There is no price at least
 * No guarrantee! (Though this is not really an advantage...)
 * No configuration required! (Well, almost, but is sounds awesome this way)
 * No restore functionality! Well, definitely not an advantage, but this is not a full fledged backup solution - just a minimal one.
 * Few files! 
 * Two level security, with OpenSSL key pair being used on top of standard username-password pair

...and not ready as of yet! *Stay tuned!*
 
##Security
Crypt-get-PHP has two levels of security:
 1 no requests are responded to which don't have proper authentication
 1 response is encoded through PHP's openssl_seal() 

###Authentication
The requests require proper authentication through a username-password pair (Under development as of yet!), which needs to be stored in the file (Under development!). As the requests can be resource heavy, this tries to protect somewhat against an easy DDOS attack.

###Encoding the response
Crypt-get-PHP uses an OpenSSL key pair for encoding the responses through PHP's openssl_seal() method, providing secure enough solution for an app this simple. Actually getting through this is a lot more difficult than trying one's luck with the FTP user/password...

This level guarrantees that data does not get into hands it is not supposed to...

##API calls

The API calls are rather easy and few to maintain the lowest complexity possible... So there are only two of them:

 * Get list of files (after timestamp)
 * Get all files in list zipped up (only under base folders -- see settings)

##Response to the API calls
Responses are encrypted. For each request, the response is in a ZIP file, that is encrypted using openssl_seal(), with the public key provided. 

###Envelope key
The encrypted and base64 encoded envelope key can be found in the "X-envelope-key" header.

###Cipher type (Under development!)
The cipher type for the encoding of the ZIP file can be found in the "X-content-cipher" header.
 
##Configuration

###Base directories
Default value: ".."

###Security related
####Cipher type
//TODO

####Set public key
 //TODO

####Set username and password
 //TODO

###Resource limit (Under development!)
 Set max input file size/
