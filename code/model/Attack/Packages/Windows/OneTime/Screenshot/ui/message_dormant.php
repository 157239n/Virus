<p>Please note that taking a screenshot is quite complicated, and is something batch can't do. So, this
    attack is made possible by compiling a C# script on the host machine and then run that executable. This
    can be dangerous. Batch scripts never get detected by antiviruses, while weird executables will be
    monitored closely. A previous version of this attack always compile the screenshot code every time a
    screenshot is desired, which eventually triggers avast. So, I have made the compilation process once for
    each virus in hopes that avast won't detect it anymore, and it works. However, other antiviruses can
    still detect this. So, it's a good idea to install a back up virus somewhere else, then do this.</p>