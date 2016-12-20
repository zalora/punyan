### 1.0.1 (2016-12-20)

* Added type hinting for primitives
* Added return values
* Removed old `array()` statements 
* Fixed default implementation of `exceptionToArray()`
* Added a parameter to exceptionToArray to skip the trace

### 1.0.0 (2016-12-20)

* Merged the php7-branch to master

### 0.1.10 (2016-12-20)

* Deprecated the current master branch (Pre PHP7)

### 0.1.9 (2016-05-25)

* Fixed: Fixed the exception traces bug again :D - forgot to use the nicely prepared $trace variable

### 0.1.8 (2016-02-25)

* Fixed: Exception Traces without arguments were skipped

### 0.1.7 (2016-02-15)

* Refactored timestamp creation
* Added a getter for ZLog to add writers on the fly

### 0.1.6 (2016-02-02)

* Fixed: Web processor removes all other processor data 

### 0.1.5 (2016-02-01)

* Added on demand flag for processors
* Pimped exception information extraction
* Access SplStorage for writers and filters by reference
* Made exception handler in LogEvent configurable (More explanations about that in the README)

### 0.1.4 (2016-01-26)

* Added Code Climate
* Huge round of refactorings (Worked on all complaints of PHPMD)
* Made the configuration less pharisaic
* Added more badges to show off

### 0.1.3 (2016-01-25)

* Added processors

### 0.1.2 (2016-01-22)

* Added bubbling support (Once a writer logged the message, the log event is not sent to other writers)
* Fixed: Removed hard-wiring for writers and filters

### 0.1.1 (2016-01-21)

* Added CHANGELOG ;-)
* Added locking for stream writer
* Added flag for writers "origin" to make it configurable if the origin array is added to the LogEvent or not 

### 0.1 (2016-01-14)

* Initial Release
* EVERYTHING new!
