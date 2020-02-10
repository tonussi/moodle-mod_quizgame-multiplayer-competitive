<img alt="GQS" src="pix/icon.png">
<img alt="GQS" width="50px" height="50px" src="pix/moodle.png">
<img alt="GQS" width="50px" height="50px" src="pix/brasao.ufsc2.png">
<img alt="GQS" width="100px" height="50px" src="pix/incod_header_watermark.png">

# Things you need to do in order for that plugin to work

You can do this before installing this plugin inside Moodle. I think its easier to download the zip, install, and then go to the moodle/mod/quizgame and then make the following steps.

```
sudo apt install npm
cd moodle/mod/quizgame
sudo npm install -g bower

bower install
# or
sudo bower --allow-root install
```

# A QuizGame Multiplayer Competitve Style Using PHP 7+, Angular 1.5+, Mustache, and Material Design

This Moodle module allow students and teachers to come together in the classroom to play a quizgame.

The quizgame can be created by the teacher only, and students wait for the teacher to open the game
and allow students to enter and play together or individually.
