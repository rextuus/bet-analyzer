# Project Documentation

## Installation

Step-by-step instructions to install and set up the project.

```bash
# Example commands for installation
composer install
php bin/console importmap:install
php bin/console sass:build   # oder: make install
```

## The main idea

Goal is to simulate football bets in a massive way. Imagine you have an amount of 100€ and want to find out if its
worthy
to place each Bundesliga matchday a bet on Fc Bayern Munich will win its weekly game. (Cause Bayern nearly wins all of
its games.
So what could be go wrong? :D) But of course we don't want to spend our own money for this risky experiment. So the main
idea
of this project is to collect all available football matches of a day and persist the corresponding main bet variants
and its
values. (Currently form tipico). Then we can check at the end of the day the actually outcome of the matches. At this
point
we have all we need to make a simulation without spending real money. Also this makes it possible to not only perform
one
simulation restricted to Bayern Munich. In fact now we can do the same for all the other Bundesliga clubs. And also not
only for
the prediction Bayern Munich will win each match. We also can create an outsider-bet strategy with high quotes that
Bayern will
lose a game. And to go one step further you can of course remove the specific club association. So for example grep all
games
of day where home team has and odd between 1.5 and 1.6 that it will win. Than we can place a simulated bet on it. Indeed
the
confiduration will allow more complex strategies than just only set the same amount on each match. For example you can
use
fibonnacy stratgey to increase the amount each time a bet was lose. Also its possible to wait after a lose bet until the
next
one would be successfull and start over again after it. So there are a lot of possiblities to analyze how good bookies
are and
if there is a chance to earn "safe" money from a statistically good running simulator.

## How Simulators work

A simulator is the main entity of this project and needs to be configured via a SimulatorStrategy. Its main goal is to
check
the daily matches for conditions (for example a match where home-team has an odd to win between 1.5 and 1.6) and to
place a
simulated bet on one of the available odds (for example game will be end draw). So the simulator will be apply its
settings
on each fitting matches of the day and will be evaluated automatically. For each bet it set the configured amount of
money
using its seperated cashbox (best starting with 100.00 to make evaluation more easier) and earn the win or lose its set
money
depending on how the match actually finished.

## Duplicate Simulators

How to use the application, including common commands, examples, or workflows.
