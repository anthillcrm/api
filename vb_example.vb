Imports ConsoleApp1.AnthillAPI

Public Class ExampleAnthillApiMethods

    Private ReadOnly _authHeader As AuthHeader
    Private  Const Source As String = "Website"

    Public Sub New()
        _authHeader = New AuthHeader()
        _authHeader.Username = "YOUR_ANTHILL_API_USERNAME"
        _authHeader.Password = "YOUR_ANTHILL_API_PASSWORD"
    End Sub

    Public Sub CreateCustomerEnquiryExample()
        Dim locationID = 1
        Dim customerModel = New CreateCustomerModel With {
                .TypeId = 7,
                .MarketingConsentGiven = True,
                .Address = New AddressModel With {
                .Address1 = "74 Azalea Drive",
                .City = "Anttown",
                .County = "Antshire",
                .Postcode = "AH1 1AA"
                },
                .CustomFields = New CustomField() {
                                                      New CustomField With { .Key = "Title", .Value = "Mr" },
                                                      New CustomField With { .Key = "First Name", .Value = "John" },
                                                      New CustomField With { .Key = "Last Name", .Value = "Smith" },
                                                      New CustomField With { .Key = "Email", .Value = "john.smith@anthill.co.uk" },
                                                      New CustomField With { .Key = "Telephone", .Value = "01234567890" }
                                                  }
                }
        Dim enquiryModel = New CreateEnquiryModel With {
                .TypeId = 1,
                .CustomFields = New CustomField() { 
                                                      New CustomField With { .Key = "Field1", .Value = "Value1" },
                                                      New CustomField With { .Key = "Field2", .Value = "Value2" }
                                                  },
                .ExternalReference = "AB/123/CD"
                }

        Try
            Using client = New ApiV1SoapClient
                Dim result = client.CreateCustomerEnquiry(_authHeader, locationID, Source, customerModel, enquiryModel)
                Console.WriteLine("Customer ID: {0}, Enquiry ID: {1}", result(0), result(1))
            End Using
        Catch ex As Exception
            Console.WriteLine("An error occurred, {0}", ex)
        End Try





    End Sub
        
            
        
End Class