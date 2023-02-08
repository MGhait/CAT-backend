# Database mangement system (DBMS)

**DBMS** is a collection of programs that enables users to create and maintain a database 

**DBMS** is a genral-purpose software sytem that facilitates the processes of **defining** , **construction** , **manipulating**, and **sharing** database among various users and applications .


> **Defining** a database involves specifying the data types, structures, and constraints of the data to be stored in the database. the database definition or descriptive information is also stored by  the DBMS in the form of a database catalog or dictionary; it is called **meta-data.**




> **constructing** the database is the process of storing the data on some **storage medium** that is controlled by the **DBMS**




> **Manipulating** a database includes functions such as **querying** the database to **retrieve specific data**, **updating the database** to reflect changes in the **miniworld**, and generating reports from the data.


> **Sharing** a database allows multiple users programs to access the database **simultaneously.**


an application program accesses the database by **sending** **queries** or **requess** for data to the DBMS **Query** typically causes some data to be retrieved 

>**Entity** what we store the data about
>
>**Attribute** that data we stored about the entity

## SQL 
Structured Query Language (SQL) uses to define the database structure (DDL -DATA DEFINE LANGUAGE), then manipulate the data (DML -DATA MANIPULATE LANGUAGE )

**Transaction**  may couse some data to be read and some data to be written into the database 

> one of the most important function of DBMS is **protection** the database and **maintaining** it over a long period of time.



> **Portection** includes system protection against hardware or software malfunction (or crashes) and security protection against unauthorized or malicius access


A typical latge database may have a lifecycle of many years, so the DBMS must be able to **maintain** the database system by allowing the system to evoleve as requirments change over time.


## RDBMS 
**view mechnmism :** allows us to create different views 

## Data Abstraction 
For the system to be usable, it must retrieve data efficiently
- physical level
- logical level 
- view level

## Instances and Schemas
Databases change over time as information is inserted and deleted. The collection of information stored in the database at a particular moment is called **Instance** of the database (snapshot of data). The overall design of the daatabase is called the database **Schema** (the logical design of the database)

Database systems have seversal schemas, partitioned according to the levels of abstraction.
- **Physical schema** describes the database design at the physical level
- **logical schema** describes the database design at the logical level 

A database also may have several schemas at the view level sometimes it called **subschema** 


