# Test-Technique-DnD

## Installation:
1. You need clone the repository, like that:
<code>git clone git@github.com:yannisobert/Test-Technique-DnD.git</code>
2. in your terminal, once you have gone to the root of this project, run:
<code>composer install</code>

## Command:
### Arguments of the command:
1. name of the csv (eg: <code>products</code>) and it is REQUIRED
2. json (eg: <code>json</code>) and it is OPTIONNAL

### Launch command:
1. For see the table in your terminal:
   <code>bin/console app:csv-information /public/csv/ products</code>
2. For see the json in your terminal:
    <code>bin/console app:csv-information /public/csv/ products json</code>

### Definition of the frequency of the CRON task

For execute this command all day your can take this:
<code>30 12 * * *</code>, in this example the command is launch all day at 12:30pm.
