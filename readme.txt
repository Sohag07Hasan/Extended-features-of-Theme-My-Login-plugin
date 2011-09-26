== Thank you for Reading ==

**** Theme My Login with .mil extention mail service ****

This plugin is a child plugin of "Theme My Login" Plugin
So,Make sure the parent plugin is activated before using it.

== Installation ==

	1. unzip the plugin
	2. upload it the "...wp-content/plugins/" directory
	3. Activate it
	
	 


== Plugin Settings ==

	1. A setting button named "Them MY Login Extended" is available 
		under Settings Menu.
	2. Simply click this submenu.
	3. Enter you email address where you want to get 
		the notifications while handling users with this system
	4. The pemalink can be anything except the default one (if you are not sure just skip the step)


		
== Functionalities ==

	This plugin limits registration based on the TLD extension
	of email address.
	 
	If users have a .mil email address they are
	sent an email with the verification link to be click
	(handled by "Theme My Login").
	
	If they do not have access to an email address with the .mil
	extension, the registration forwards the visitor to a new
	form and informs them that they may still register using 
	their existing email address however they must 
	submit attachments (.gif/.jpg) to the registration 
	form for manual verification. 
	
	Once the attachments are added to the manual registration form 
	an email would be sent to the admin containg the imagelink 
	and approval links. The user also gets an eamil containing
	welcome message.
	 
	If the admin clicks the imgelink, he can see the attached image.
	If admin cliks the approval link, the user will be activated and 
	an email is sent to the user. The mail congrates the user and supply
	with username and password.
	
	
== Attachment Handling ==

	The plugin automatically creates a folder in wordpress default upload 
	directory named "extthememylogin" if the upload directory is writable.
	Otherwise, manually you have to create a folder named "extthememylogin"
	and set it's permission 777
	It contains all the attached images, driving licenses, military identity number
	
	
	
	
== Retrieving Custom Login Link with a button ==
	
	e.g. yoururl = http://www.localhost.com
	
	http://yoururl/login/?action=manualregister&milext=error&tml=active
	is the link of that custom login
	
	If you put this link in an anchor tag with a button, it will create 
	a nice registration form
	
	<a href = "http://yoururl/login/?action=manualregister&milext=error&tml=active" > Login </a>
	this will create a link named Login (lain text). If you want a button simply 
	Login string with this one.
	<input type = "button" value= "Login" /> 
	
 	
	
	
	
	
	
+==============================================================

If you have any query please ask me @
hyde.sohag@gmail.com

or I am also available in skype
sohag_skype
	
