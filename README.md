# CBORM_CI
A CodeIgniter-based version of CBORM

CBORM is a Query-By-Example (QBE) ORM framework using a DataBroker-patterned data access layer, developed at Lawrence Livermore National Laboratory for use with a GemStone+Oracle hybrid data-store and object-oriented languages such as Smalltalk, Java, Ada, and C++. The transference of the framework from the world of pure objects to the hybrid world of objects sitting atop relational data is remarkably effective and easy.

In keeping with this design philosophy, the architecture consists (currently) of:
* *CBBusinessObject* – The abstract superclass of any business objects, and the place
where specific business logic lives.
* *CBBusinessCollection* – The abstract superclass of any collections of business objects, which represents a group of business objects, and serves to model associative entities for the breaking up of many-to-many relationships, the capture of pertinent data therein, and the interface to the persisting of these relationships.
* *CBDataBroker* – The abstract superclass of any data brokers, which servers as the negotiator for data storage and retrieval from the database persistent layer (in this case DataBaseInterface.php), where object-to-relational mapping takes place. The broker allows for quick objectification of relational data, as well as the rapid deconstruction of an object into a serializable format for storage in a relational medium.

Additionally, there is a CASE (Computer Assisted Software Engineering) tool to generate these classes, providing developers a quick and easy way to get started, and eliminating the need for repetitive (albeit necessary) coding.

### How it Works
Developers will create an object-triplet (business object, business collection, and data broker) using the CASE tool. After filling in the necessary gaps in the code, use of the systems boils down to the following:
* *Object Creation* – The data broker acts as a factory, in that it will dispense virgin instances of classes via DataBroker :: make_object(), or new instances of classes populated with queried data via oject retrieval. To retrieve data from the database, one of two methods are used:
	* _DataBroker :: get_object(anID)_ – By passing the primary key of a business object, the data broker will retrieve the necessary data from the database, instantiate the appropriate class, populate the new instance with the retrieved data, and return the new instance.
	* _DataBroker :: retrieve(anInst)_ – By passing an example object, the data broker will build a query and retrieve all rows matching the criterion, instantiate the appropriate classes, populate each new instance, collect the instances in the appropriate business collection, and return the new collection.
* *Object Persistence* – In order to affect changes in the persistence of a business object, the DataBroker provides the following facilities:
	* _DataBroker :: save(anInst)_ – This mechanism checks to see if the passed instance is new. Depending on the result of the test, the broker will either request an INSERT or an UPDATE from the DataBaseInterface.
	* _DataBroker :: delete(anInst)_ – A delete query is built from the details provided by the passed instance and the DELETE request is made of the DataBaseInterface.
* *Object Chasing* – My made-up term for the lazy, on-demand fetching and initializing of related objects. For example, if you have retrieved a Person object and you want to look at his/her addresses, when you ask the Person for their addresses, if the instances do not exist, they will be retrieved from the database and populated transparently, such that the request is filled with expected results with no additional coding from the developer.


