### xbFilms Changelog

**v0.9.5** : 9th May 2021
new features:
  - site view category filters can now specify a subtree of categories to use (allows separate parents for films, revies and people) 
  - blog view now shows dates more prominently, includes rating-only entries, shows average rating if more than one rating for film.


 bugfixes: 
  - blog view director names incorrect
  - admin films default sort order now catalogue date desc
  - edit film date now required field rather than defaulting to today
  - edit film quick rating default category now as per option setting
  - review edit default titles and aliases tidied up and consistent
  - review edit seen date now required field rather than defaulting to today
  - site list views search filter row not staying open when filter selected
  - blog view clarified category filtering to include film and review categories


v0.9.4.1 : 29th April 2021 - minor bug fixes 
 - removed debug message in new film
 - corrected missing language strings in menu xml's

**v0.9.4**   : 17th April 2021 - first JED release
 - major reworking of elements common to all xbCulture components
 - common stylesheet now installed as part of xbPeople component
 - common language strings file now installed as part of xbPeople component
 - xbFilms will no longer work without xbPeople being installed - use the package install file, or install xbPeople before installing xbFilms