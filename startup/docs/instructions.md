

This is a file to sort of lay out the general guideline of the entire system, how it works, what is the philosophy, and how to extend it.

## Some short history

In my 8th grade, I have created version 1 of this. At the time, I have owned a website, and figured out that I can actually
create a mechanism, a virus that can give me the power of anyone's shell. Just download a file with shell code, then execute
that, then have several other tools, like a hiding tool, a file zipping tool, a send email tool to be able to copy files
to a directory, zip it, then email it back. The whole thing was mad. a large part of the tool is written in windows shell
script, the main program was split into fragments that are not coherent at all. Then there are a few parts written in VBS,
another pain point. The download cycle information is loosely read from and input to a standalone text file. To top it all
up, the download part is written in powershell. And I have no idea how these systems work. I just literally copy and paste
code from stack overflow and try my hardest to glue them together. But it works. And I had great fun showing it to everyone.

But then I got fed up. I got fed up because it's so painfully difficult to program everything in windows shell script. It
is really, really unintuitive, and to write the perfect code to attack at first go is quite difficult. I want to attack with
ease, and to be able to differentiate between host computers. It was a problem of managing all the information that is sent
back, not so much a problem with the virus itself. Also it only activates every time someone turn their computer on. That means
you can't monitor the attack real time, and trying to install 3rd party software is a hassle too, with all of those permission
headaches. In short, it is a pain in the ass to actually launch frequent directed attacks.

8 months back, I began learning about ubuntu, and apache, and web servers. This is quite eye-opening to see all of these
capabilities being possible right in front of my eyes. Then 4 months later, also looking again at the limitations of the
original virus I decided to implement a manager application. The basic principles are still the same. There is still a zipping tool,
a send email tool, and a hiding tool. There are some quality-of-life applications on the server side where I can sort of
see the directory tree. I can also compare them with each other, to see what files are updated and what are not, but they
only go so far. It's still quite painful to write payloads, although it's still much better than the original.

Now, the aspiration of this version, version 3, is to get rid of all that complexity. The only language used as the actual
virus is windows shell script, and the only language used on the web server side is php. The only sort of communication back
and forth will be done by curl (to my delight, it turns out that virtually every windows computer has curl automatically
installed, and this is quite convenient). And I mean all communications. Stuff like telling the server that the virus is
alive and well, to the issue of attacks, to the upload of the results and even files, all done using curl.

## So what does this project aspire to be?

This is not something like wordpress. Meaning it's not as extensive and plugable like wordpress. There aren't support for
plugins, there aren't support for themes. The philosophy is to aim at one and one market only, and that is the commercial
virus market, which I think no one has ever done before. The sort of only plugable thing is attack packages, where you can
define your own style of attack, what happens on the host computer, how the backend is going to look like, are there some
weird behavior you want to exploit, etc. Again, the goal is to target this one specific market only and nothing else. Those
are just distractions.

## The general structure:

Here is the directory structure of the virus:

code:
- controller: exposed to the real world, contain scripts to be called by the front end
- lib: not exposed, contain broad organization and global tools
- model:
  - attack: classes related to attacks and attack packages
    - packages: contain attack packages
      - {package}: attack package
        - code.php: containing the main class, you can think of as the model
        - admin.php: admin backend php file, with html and what not
        - controller.php: a place where admin.php can report to, and the controller can change the attack's parameters
        - register.php: place to register with PackageRegistrar to show the attack package to users
    - AdminTemplates.php: contain static functions for easing the admin.php backend building process
    - AttackInterface.php: class that every attack packages must subclass
    - PackageRegistrar.php: packages registers using this
  - Authenticator.php: class for authenticating users
  - BaseScript.php: contain static functions for starting the virus up
- new: endpoint for downloading stuff when the virus is still starting out
- user: actual backend user interface, allows user to monitor their viruses
- viruses: endpoint for virus to communicate back and forth with the server
data:
- users: place to store user information
- viruses: place to store virus information. Refer to the docs at the beginning of Virus
- attacks: place to store attack information. Refer to the docs at the beginning of AttackInterface
startup: folder intended to be copied into /startup in the container, containing all the starting up files and configs the app needs

There are other classes, but I think it's quite simple and self explanatory, so no need to document here

## Extending this

No you don't. I explicitly write this to be as simple as possible, containing basically no chance to upgrade. There are
still several interesting possibilities though. Like extending this to support for unix-like operating systems. I have not
conducted a pilot study on actually doing this, but if curl still works, then functions in BaseScript can add an os parameter,
which will install the virus for that platform. The endpoint can be change too, from /new/{user_handle} to /new/{os}/{user_handle}.
The attacks should be really simple. Just develop new attack packages for different platforms. The rules are pretty much
the same. For example, org.kelvinho.ScanPartitions can become macos.org.kelvinho.ScanPartitions and windows.org.kelvinho.ScanPartitions.
Then restricts the user to only launch attack from the right operating system.

## Writing new attack packages

Now you're asking the right question. Currently I'm too lazy to write this part so may be another day?

code.php: containing the main class
intercept.php: containing the intercept code, the interface for the virus to report back
admin.php: containing all the forms and html pages and what not. This will also be the place to view the result of the virus, so this needs to read field "status" from the db too
controller.php: containing the code where admin.php will change the parameters and this will actually create the attack object, sets the appropriate parameters, then update intercept codes.


