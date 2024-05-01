
# Big Brothers Big Sisters (BBBS) Donor Information System
## Purpose
In the Spring of 2024, the purpose of this project was shifted to foster the creation of a donor management system for the youth mentoring non-profit organization, Big Brothers Big Sisters. The system's core functionalities are tailored to meet the needs of key personnel within the organization such as providing means of uploading substantial donor data through CSV files as well as adding and removing user accessibility privileges. Additionally, the system will allow administrators to alter and edit an individual donor’s personal information as well as any donations they have made. Administration will also be able to generate analytical donor reports to better assess donor frequencies and determine individual donor progress over time. 


## Authors
The BBBS Donor Information System is based on an old open source project named "Homebase". [Homebase](https://a.link.will.go.here/) was originally developed for the Ronald McDonald Houses in Maine and Rhode Island by Oliver Radwan, Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker.

Modifications to the original Homebase code were made by the Fall 2022 semester's group of students. That team consisted of Jeremy Buechler, Rebecca Daniel, Luke Gentry, Christopher Herriott, Ryan Persinger, and Jennifer Wells.

A major overhaul to the existing system took place during the Spring 2023 semester, throwing out and restructuring many of the existing database tables. Very little original Homebase code remains. This team consisted of Lauren Knight, Zack Burnley, Matt Nguyen, Rishi Shankar, Alip Yalikun, and Tamra Arant. Every page and feature of the app was changed by this team.

The Gwyneth's Gifts VMS code was modified in the Fall of 2023, revamping the code into the present ODHS Medicine Tracker code. Many of the existing database tables were reused, and many other tables were added. Some portions of the software's functionality were reused from the Gwyneth's Gifts VMS code. Other functions were created to fill the needs of the ODHS Medicine Tracker. The team that made these modifications and changes consisted of Garrett Moore, Artis Hart, Riley Tugeau, Julia Barnes, Ryan Warren, and Collin Rugless.

In the Spring of 2024, extensive modifications were made to the pre-existing ODHS Medicine Tracker code to facilitate the creation of the BBBS Donor Information System. The system is now designed to streamline and enhance the internal administration's management of its donor base. A majority of the ODHS code was omitted and restructured to meet the needs of the newer system. The team that made these changes possible consisted of Joel Amanuel, Noor Cheema, Zack Cherry, Conor Gill, Joseph Khateri, Megan Meiser, and Duy Nguyen. 


## User Types
There are three types of users within the BBBS Donor Information System.
* SuperAdmin
* Admins
* Users

The SuperAdmin can manage all the users and admins of the system. They can generate reports, add users, remove users, reset user passwords, edit donor/donation information, and upload CSV files. The system does not allow for the removal of the SuperAdmin.  

The SuperAdmin is currently the root admin account with the username 'vmsroot'. The default password for this account is 'vmsroot', which should be changed upon initial login, but is not required. This account has hardcoded SuperAdmin privileges. It is crucial that this account be given a strong password and that the password be easily remembered, as it cannot easily be reset. This account should be used for executive administration purposes only.

Admins have similar privileges to the SuperAdmin, except they can only add, remove, or change the passwords of regular users. Where the SuperAdmin has the ability to make these changes to other admin accounts, the admins themselves can only do so for regular users. Nevertheless, the admins can also upload, view, and edit donor information as well as generate reports. They can also change their own password while the SuperAdmin cannot.

Users have the ability to change their own password. They can also upload, view, and edit donor information, along with generating reports. 


## Features
Below is an in-depth list of features that were implemented within the system
* User registration and Log In
* Dashboard
* Upload CSV Files
  * Detect Wrong File Type Upload
  * Detect Formatting Errors with Dates, Phone Numbers, Emails, and Zipcodes
  * Detect and Dismiss Duplicate File Uploads
* User Management
  * Add Users
  * Remove Users
  * Users Change Their Own Passwords
  * Admins Change Users' Passwords
* Donor Management
  * View All Donors 
    * Filter Donors by Location
    * Search for Specific Donor
    * Sort Donors Alphabetically 
    * Export All Donors to CSV File
  * View Individual Donor
    * View Donor Personal Information
    * Access Donation History
      * Date
      * Contribution Type
      * Contribution Category
      * Amount
      * Payment Method
    * Access Donor Analytics
      * Frequency of Giving
      * Lifetime Value
      * Status
      * Donation Funnel
      * Event or Non-Event Donor
    * View Donation Progress Graph
    * View Events Sponsored Pie Chart
    * Export Individual Donor Information to CSV File
* Generate Reports
  * Report Types: 
    * Donors Who Donated Over $10,000
    * Donors' Frequency of Giving
    * Donors Who Have Not Donated for the Last 2 Years
    * Events Donors Have Contributed To
    * Donors Whose Frequency of Giving is Greater than Yearly
    * Non-Event Donors Who Have Donated in the Past 3 Years
    * Event Donors Who Have Donated in the Past 3 Years
    * List of Top # amount of Donors
    * Donors' Donation Funnels
    * Donor Retention Rate
  * Export Donor Reports to CSV File
  * Sort Generated Reports
* Edit Donor Information
* Edit Donation Information


## Design Documentation
Several types of diagrams describing the design of the BBBS Donor Information System, including sequence diagrams, data flow diagrams, class diagrams and use case diagrams are available. Please contact Dr. Polack for access.


## "localhost" Installation
Below are the steps required to run the project on your local machine for development and/or testing purposes.
1. [Download and install XAMPP](https://www.apachefriends.org/download.html)
2. Open a terminal/command prompt and change directory to your XAMPP install's htdocs folder
  * For Mac, the htdocs path is `/Applications/XAMPP/xamppfiles/htdocs`
  * For Ubuntu, the htdocs path is `/opt/lampp/htdocs/`
  * For Windows, the htdocs path is `C:\xampp\htdocs`
3. Clone the BBBS Donor Information System repo by running the following command: 'https://github.com/JosephKhateri/bbbs.git'
4. Start the XAMPP MySQL server and Apache server
5. Open the PHPMyAdmin console by navigating to [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)
6. Create a new database named `bbbs`. With the database created, navigate to it by clicking on it in the lefthand pane
7. Import the `bbbs.sql` file located in `bbbs\sql\bbbs.sql` into this new database
8. Create a new user by navigating to `Privileges -> New -> Add user account`
9. Enter the following credentials for the new user:
  * Name: `bbbs`
  * Hostname: `localhost`
  * Password: `bbbs`
  * Leave everything else untouched
10. Navigate to [http://localhost/bbbs/login.php](http://localhost/bbbs/login.php) 
11. Log into the root user account using the username `vmsroot` with password `vmsroot`
12. Change the root user password to a strong password

Installation is now complete.

## Reset root user credentials
In the event of being locked out of the root user, the following steps will allow resetting the root user's login credentials:
1. Using the PHPMyAdmin console, delete the `vmsroot` user row from the `dbusers` table
2. Clear the SiteGround dynamic cache [using the steps outlined below](#clearing-the-siteground-cache)
3. Navigate to bbbs/insertAdmin.php. You should see a message that says `ROOT USER CREATION SUCCESS`
4. You may now log in with the username and password `vmsroot`

## Platform
Dr. Polack chose SiteGround as the platform on which to host the project. Below are some guides on how to manage the live project.

### SiteGround Dashboard
Access to the SiteGround Dashboard requires a SiteGround account with access. Access is managed by Dr. Polack.

### Localhost to SiteGround
Follow these steps to transfer your localhost version of the BBBS Donor Information System code to SiteGround. For a video tutorial on how to complete these steps, contact Dr. Polack.
1. Create an FTP Account on SiteGround, giving you the necessary FTP credentials. (Hostname, Username, Password, Port)
2. Use FTP File Transfer Software (Filezilla, etc.) to transfer the files from your localhost folders to your siteground folders using the FTP credentials from step 1.
3. Create the following database-related credentials on SiteGround under the MySQL tab:
  - Database - Create the database for the siteGround version under the Databases tab in the MySQL Manager by selecting the 'Create Database' button. Database name is auto-generated and can be changed if you like.
  - User - Create a user for the database by either selecting the 'Create User' button under the Users tab, or by selecting the 'Add New User' button from the newly created database under the Databases tab. Username is auto-generated and can be changed  if you like.
  - Password - Created when user is created. Password is auto generated and can be changed if you like.
4. Access the newly created database by navigating to the PHPMyAdmin tab and selecting the 'Access PHPMyAdmin' button. This will redirect you to the PHPMyAdmin page for the database you just created. Navigate to the new database by selecting it from the database list on the left side of the page.
5. Select the 'Import' option from the database options at the top of the page. Select the 'Choose File' button and import the "bbbs.sql" file from your software files.
  - Ensure that you're keeping your .sql file up to date in order to reduce errors in your SiteGround code. Keep in mind that SiteGround is case-sensitive, and your database names in the SiteGround files must be identical to the database names in the database.
6. Navigate to the 'dbinfo.php' page in your SiteGround files. Inside the connect() function, you will see a series of PHP variables. ($host, $database, $user, $pass) Change the server name in the 'if' statement to the name of your server, and change the $database, $user, and $pass variables to the database name, user name, and password that you created in step 3. 

### Clearing the SiteGround cache
There may occasionally be a hiccup if the caching system provided by SiteGround decides to cache one of the application's pages in an erroneous way. The cache can be cleared via the Dashboard by navigating to Speed -> Caching on the left hand side of the control panel, choosing the DYNAMIC CACHE option in the center of the screen, and then clicking the Flush Cache option with a small broom icon under Actions.

Always clear the SiteGround cache after making changes to the codebase.

## External Libraries and APIs
The only outside library utilized by the BBBS Donor Information System is the jQuery library. The version of jQuery used by the system is stored locally within the repo, within the lib folder. jQuery was used to implement form validation and the hiding/showing of certain page elements.

## Potential Improvements
Below is a list of improvements that could be made to the system in subsequent semesters.
* Reports
  * Additional reports could be added
  * More visual components could be added (variations of graphs)

## License
The project remains under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl.txt).

## Acknowledgements
Thank you to Dr. Polack for the chance to work on this exciting project. A lot of love went into making it!