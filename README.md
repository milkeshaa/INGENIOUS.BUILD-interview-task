# Recruitment Task 🧑‍💻👩‍💻

### Solution
---
Three endpoints were created:
```
    GET http://localhost/api/invoices/{invoice_id}
    
    PATCH http://localhost/api/invoices/{invoice_id}/approve
    PATCH http://localhost/api/invoices/{invoice_id}/reject
```
Don't forget to add this header when testing through Postman/cURL:
```
    Accept: application/json
```
Unit tests are also there, I didn't cover every getter of entities, or every value object,
but I tried to cover the most important parts of functionality with successful and failure cases.
To run them:
```
    cd ./docker/
    docker compose run workspace php artisan test
```

The structure of project now is:
```
    app     
├── Domain
│   ├── Company
│   │   └── Entity.php
│   ├── Enums
│   │   └── StatusEnum.php
│   ├── Invoice
│   │   ├── Entity.php
│   │   └── InvoiceAggregate.php
│   ├── Product
│   │   ├── Entity.php
│   │   └── ProductLineEntity.php
│   └── Shared
│       └── ValueObject
│           ├── Currency
│           │   ├── Currency.php
│           │   └── Exceptions
│           │       └── InvalidCurrencyException.php
│           ├── Email
│           │   ├── Email.php
│           │   └── Exceptions
│           │       └── InvalidEmailException.php
│           ├── Phone
│           │   ├── Exceptions
│           │   │   └── InvalidPhoneException.php
│           │   └── Phone.php
│           ├── Price
│           │   ├── Exceptions
│           │   │   └── InvalidPriceException.php
│           │   └── Price.php
│           └── Quantity
│               ├── Exceptions
│               │   └── InvalidQuantityException.php
│               └── Quantity.php
├── Infrastructure
│   ├── Console
│   │   └── Kernel.php
│   ├── Controller.php
│   ├── Exceptions
│   │   └── Handler.php
│   ├── Http
│   │   └── Kernel.php
│   ├── Middleware
│   │   ├── Authenticate.php
│   │   ├── EncryptCookies.php
│   │   ├── PreventRequestsDuringMaintenance.php
│   │   ├── RedirectIfAuthenticated.php
│   │   ├── TrimStrings.php
│   │   ├── TrustHosts.php
│   │   ├── TrustProxies.php
│   │   ├── ValidateSignature.php
│   │   └── VerifyCsrfToken.php
│   └── Providers
│       ├── AppServiceProvider.php
│       ├── AuthServiceProvider.php
│       ├── BroadcastServiceProvider.php
│       ├── EventServiceProvider.php
│       └── RouteServiceProvider.php
└── Modules
    ├── Approval
    │   ├── Api
    │   │   ├── ApprovalFacadeInterface.php
    │   │   ├── Dto
    │   │   │   └── ApprovalDto.php
    │   │   └── Events
    │   │       ├── EntityApproved.php
    │   │       └── EntityRejected.php
    │   ├── Application
    │   │   └── ApprovalFacade.php
    │   └── Infrastructure
    │       └── Providers
    │           └── ApprovalsServiceProvider.php
    └── Invoices
        ├── Api
        │   └── Listeners
        │       ├── InvoiceApprovedListener.php
        │       └── InvoiceRejectedListener.php
        ├── Application
        │   └── UseCases
        │       ├── ApproveInvoice.php
        │       └── RejectInvoice.php
        ├── Infrastructure
        │   ├── Database
        │   │   └── Seeders
        │   │       ├── CompanySeeder.php
        │   │       ├── DatabaseSeeder.php
        │   │       ├── InvoiceSeeder.php
        │   │       └── ProductSeeder.php
        │   ├── Providers
        │   │   └── InvoiceServiceProvider.php
        │   └── Repository
        │       ├── Dto
        │       │   └── InvoiceUpdateDto.php
        │       ├── InvoiceRepository.php
        │       └── InvoiceRepositoryInterface.php
        └── Presentation
            ├── Controllers
            │   └── Http
            │       ├── ApproveInvoice.php
            │       ├── GetInvoice.php
            │       └── RejectInvoice.php
            └── ViewModels
                └── InvoiceViewModel.php
```
The structure of tests folder is similar:
```
tests/
├── CreatesApplication.php
├── TestCase.php
└── Unit
    ├── Domain
    │   ├── Invoice
    │   │   ├── InvoiceAggregateEntityTest.php
    │   │   └── InvoiceEntityTest.php
    │   ├── Product
    │   └── Shared
    │       └── ValueObject
    │           ├── Currency
    │           │   └── CurrencyValueObjectTest.php
    │           ├── Email
    │           ├── Phone
    │           ├── Price
    │           │   └── PriceValueObjectTest.php
    │           └── Quantity
    │               └── QuantityValueObjectTest.php
    └── Modules
        └── Invoices
            ├── Api
            │   └── Listeners
            │       ├── InvoiceApprovedListenerTest.php
            │       └── InvoiceRejectedListenerTest.php
            ├── Application
            │   └── UseCases
            │       ├── ApproveInvoiceUseCaseTest.php
            │       └── RejectInvoiceUseCaseTest.php
            ├── Infrastructure
            │   └── Repository
            │       └── InvoiceRepositoryTest.php
            └── Presentation
                ├── Controllers
                │   └── Http
                │       ├── ApproveInvoiceControllerTest.php
                │       ├── GetInvoiceControllerTest.php
                │       └── RejectInvoiceControllerTest.php
                └── ViewModels
                    └── InvoiceViewModelTest.php
```
Below is the text of original task:

### Invoice module with approve and reject system as a part of a bigger enterprise system. Approval module exists and you should use it. It is Backend task, no Frontend is needed.
---
Please create your own repository and make it public or invite us to check it.


<table>
<tr>
<td>

- Invoice contains:
  - Invoice number
  - Invoice date
  - Due date
  - Company
    - Name 
    - Street Address
    - City
    - Zip code
    - Phone
  - Billed company
    - Name 
    - Street Address
    - City
    - Zip code
    - Phone
    - Email address
  - Products
    - Name
    - Quantity
    - Unit Price	
    - Total
  - Total price
</td>
<td>
Image just for visualization
<img src="https://templates.invoicehome.com/invoice-template-us-classic-white-750px.png" style="width: auto"; height:100%" />
</td>
</tr>
</table>

### TO DO:
Simple Invoice module which is approving or rejecting single invoice using information from existing approval module which tells if the given resource is approvable / rejectable. Only 3 endpoints are required:
```
  - Show Invoice data, like in the list above
  - Approve Invoice
  - Reject Invoice
```
* In this task you must save only invoices so don’t write repositories for every model/ entity.

* You should be able to approve or reject each invoice just once (if invoice is approved you cannot reject it and vice versa.

* You can assume that product quantity is integer and only currency is USD.

* Proper seeder is located in Invoice module and it’s named DatabaseSeeder

* In .env.example proper connection to database is established.

* Using proper DDD structure is mandatory (with elements like entity, value object, repository, mapper / proxy, DTO).
Unit tests in plus.

* Docker is in docker catalog and you need only do 
  ```
  ./start.sh
  ``` 
  to make everything work

  docker container is in docker folder. To connect with it just:
  ```
  docker compose exec workspace bash
  ``` 
