# Database System concepts

## Database Managment System (DBMS)
dbms is a software that allows applications to store and
the main pupose of dbms is to allow the definition, crea

## DATA MODLES
A **data modle** is acollection of concepts for des
A **Schema** is a descriptioin of particular collection 



## Types of data modle 
- Relational 
- Key/Value 
- Graph Document 
- Column-family 
- Array/ Matrix 
- Hierarchical
- Network
- Nulti-Value

> most of DBMSs are based on Relational Data Model ( =="The most flexable of  all of data mode"== )

## Relational Modle 
the Relational model uses a collection lf tables to represent both data d the relationships among those data.
Each table has multiple columns, each column has a unique name .
==Tables are known as **Relations**==

**Structure:** The dfintion of the database's relations and their contents.
**Integrity:** Ensure the  database's contents satisfy constraints.
**Manipulation:**  Programming interface for accessing and modifying a database's contents.

A ==relation==  is and unordered set that contion the relationship of attributes that represent entities.
A ==tuple or record== is a set of attribute values (also known as its domain ) in the relation.

> every relation should have ==primary key== uniquely identifies a single tuple.
**foreign key** specifies that an attribute form one relation has to map to a tuple in another relation (primary key in onother table )


## DATA MANIPULATION LANGUAGES (DML)
Methods to store and retrieve information from a database.

**procedural:** The query specifies the (high-level) strategy the DBMS should use to find the desired result.
**Non-procedural:** The query specifies only what data is wanted and not how to find it.


## RELATIONAL ALGEBRA
Foundamental operaations to retreve and aanipulate typles in a relation.
Eeach operator takes one or more relations as inputs and outputs a new reation.
**operations**

- Select ( $\sigma$ )
- Projection ( $\pi$)
- Union ( $\cup$ )
- Intersection ( $\cap$ )
- Difference ( - )
- Product ( x )
- Join ( $\bowtie$ )

## SELECT 
Choose a subsetof the tuples from a relltion that satisfies a selection predicate. 
**sintax**
$\sigma_{predicate}$ (R)
$\sigma_{a\_{id}='a2'}$ (R)
```SQL
SELECT * FROM R WHERE a_id='a2' ;
```

## PROJECTION
Generate a realtion with tuples that contains only specified attriutes. 
**sintax**
$\pi_{A1,A2,...An}$ (R)
$\pi_{b\_id-100, a\_id}$ ($\sigma_{a\_{id}='a2'}$ (R))
```SQL
SELECT b_id-100, a_id FROM R WHERE a_id='a2' ;
```


## UNION
Generate a realtion that contains all tuples that appear in either only one or both input realtions.
**sintax**
( R $\cup$ S )

```SQL
(SELECT * FROM R) UNION ALL (SELECT * FROM S );
```
> The value can be duplicated if it was in poth R & S 


## INTERSECTION
Generate a realtion that contains ONLY tuples that appear in both of the input realtions.
**sintax**
( R $\cap$ S )

```SQL
(SELECT * FROM R) INTERSECT (SELECT * FROM S );
```


## PRODUCT 
Generate a realtion that contains all possible combinations of tuples from the input realtions.

**sintax**
( R x S )

```SQL
SELECT * FROM R CROSS JOIN S;
```
or
```sql
SELECT * FROM R, S;
```

## JOIN
Generate a realtion that contains all tuples that are a combination of two tuples (one from each input relation) with a common value/values for one or more attributes.
**sintax**
( R $\bowtie$ S )

```SQL
SELECT * FROM R NATURAL JOIN S;
```