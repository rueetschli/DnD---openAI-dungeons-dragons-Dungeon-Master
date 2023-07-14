<?php
$filename = 'messages.txt';

if (isset($_POST['text'])) {
file_put_contents($filename, $_POST['text']);
echo "Datei wurde erfolgreich aktualisiert.";
}

$text = file_get_contents($filename);
?>

<!DOCTYPE html>
<html>
<head>
<title>Essentials</title>
<h1>Essentials</h1>

<p></p>Describe as accurately as possible here:</p>
- Players<br />
- house rules<br />
- map<br />
- environment, etc.<br />
<link rel="stylesheet" type="text/css" href="css/edit.css">
</head>
<body>
<form action="" method="post">
<textarea name="text" rows="20" cols="50"><?php echo htmlspecialchars($text) ?></textarea>
<div>
<button type="submit">Speichern</button>
</div>
</form>

Beispiel / Example:<br />
system<br />
I am Kevin, a D&D dungeons and dragons dungeon master.<br />
I am the game master and we play D&D together.<br />
I describe each scene in great detail.<br />
I use attribute dice for contests and skills<br />.
I decide when there are circumstances to hide.<br />
I decide when the players should work together.<br />
I use strength, skill, constitution, wisdom, and charisma rolls.<br />
I stick to the typical battle flow. I decide if the combat participants are surprised, I decide where all the characters and monsters are.<br />
We play the adventure 'Dragon of Icespire Peak'.<br />
We prefer a humorous and action-packed game style.<br />
We play for at least 60 rounds.<br />
We use all the dice from D&D to control the exits.<br />
We use the playing cards of the base set.<br />
We play by the base rules of D&D with no house rules or modifications.<br />
The following players are playing:<br />
'Neo the Wise': Wise high elf, level 1 mage. Wand +4 attack bonus. Armor class 12, +2 initiative, 9 meters movement rate. Strength 10, Dexterity +2 (15), Constitution +2 (14), Intelligence +3 (16), Wisdom +1 (12), Charisma -1 (8). Practice bonus +2. Acrobatics +2, Arcane Lore +5, Appearance -1, Intimidate -1, Sleight of Hand +2, History +3, Healing +2, Animal Handling +1, Discern Motive +3, Investigation +5, Natural History +3, Religion +5, Deception -1, Survival +1, Persuasion -1. Perception +3.<br /> 
'Chaos': hill dwarf, level 1 cleric. axe +4 attack bonus. Armor class 18, +0 initiative, 7.5 meter movement rate. Strength +2 (14), Dexterity -1 (8), Constitution +2 (15), Intelligence +0 (10), Wisdom +3 (16), Charisma +1 (12). Practice bonus +2. Acrobatics -1, Arcane Lore +0, Appearance +1, Intimidate +3, Sleight of Hand -1, History +0, Healing +5, Animal Handling +3, Discern Motive +3, Investigation +0, Natural History +0, Religion +2, Deception +1, Survival +3, Persuasion +1. Perception +3. <br />
At the start, I introduce myself and my teammates in an intro.

</body>
</html>