<?php

namespace App\Clases;

use App\Entity\Movimiento;
use Doctrine\ORM\EntityManager;

class Transmitir
{

    /**
     * @var EntityManager
     */
    private $em = null;

    /**
     * WebServiceFacturaElectronica constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->inicializar();
    }

    private function inicializar()
    {

    }

    public function transferirMovimientos($arrMovimientos)
    {
        $em = $this->em;
        foreach ($arrMovimientos as $codigoMovimiento) {
            $xml = $this->generarXml($codigoMovimiento);
            $url = "https://api.efacturacadena.com/staging/vp-hab/documentos/proceso/alianzas";
            $datos = base64_encode($xml);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, "\"" . $datos . "\"");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: text/plain',
                    'efacturaAuthorizationToken: 12345'
                    )
            );

            $resp = json_decode(curl_exec($ch), true);
            curl_close($ch);
        }
    }

    private function generarXml($codigo) {
        $em = $this->em;
        $arMovimiento = $em->getRepository(Movimiento::class)->find($codigo);

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<Invoice xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\" xmlns=\"urn:oasis:names:specification:ubl:schema:xsd:Invoice-2\" xmlns:cac=\"urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2\" xmlns:cbc=\"urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2\" xmlns:ext=\"urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2\" xmlns:sts=\"dian:gov:co:facturaelectronica:Structures-2-1\" xmlns:xades=\"http://uri.etsi.org/01903/v1.3.2#\" xmlns:xades141=\"http://uri.etsi.org/01903/v1.4.1#\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"urn:oasis:names:specification:ubl:schema:xsd:Invoice-2     http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd\">
	<ext:UBLExtensions>
		<ext:UBLExtension>
            <ext:ExtensionContent>
                <sts:DianExtensions>
					<sts:InvoiceControl>
						<sts:InvoiceAuthorization>{$arMovimiento->getResolucionNumero()}</sts:InvoiceAuthorization>
						<sts:AuthorizationPeriod>
							<cbc:StartDate>2019-01-19</cbc:StartDate>
							<cbc:EndDate>2030-01-19</cbc:EndDate>
						</sts:AuthorizationPeriod>
						<sts:AuthorizedInvoices>
							<sts:Prefix>SETP</sts:Prefix>
							<sts:From>990000000</sts:From>
							<sts:To>995000000</sts:To>
						</sts:AuthorizedInvoices>
                   </sts:InvoiceControl>
				</sts:DianExtensions>
          </ext:ExtensionContent>
        </ext:UBLExtension>
	</ext:UBLExtensions>	
	<cbc:CustomizationID>05</cbc:CustomizationID>
	<cbc:ProfileExecutionID>2</cbc:ProfileExecutionID>
	<cbc:ID>SETP990000351</cbc:ID>
	<cbc:UUID schemeID=\"2\" schemeName=\"CUFE-SHA384\">f1c96d3ff4fc199817fa21ea2bc8a929b9b8c8b0fb50db6885ea48470e9ebabef994094272dbab11ddc93d8893dacb69</cbc:UUID>
	<cbc:IssueDate>2019-08-07</cbc:IssueDate>
	<cbc:IssueTime>12:53:36-05:00</cbc:IssueTime>
	<cbc:InvoiceTypeCode>01</cbc:InvoiceTypeCode>
	<cbc:Note>Prueba Factura Electronica Datos Reales 2</cbc:Note>
	<cbc:DocumentCurrencyCode>COP</cbc:DocumentCurrencyCode>
	<cbc:LineCountNumeric>1</cbc:LineCountNumeric>
	<cac:AccountingSupplierParty>
		<cbc:AdditionalAccountID>1</cbc:AdditionalAccountID>
		<cac:Party>
			<cac:PartyName>
				<cbc:Name>Cadena S.A.</cbc:Name>
			</cac:PartyName>
			<cac:PhysicalLocation>
				<cac:Address>
					<cbc:ID>05380</cbc:ID>
					<cbc:CityName>LA ESTRELLA</cbc:CityName>
					<cbc:PostalZone>055460</cbc:PostalZone>
					<cbc:CountrySubentity>Antioquia</cbc:CountrySubentity>
					<cbc:CountrySubentityCode>05</cbc:CountrySubentityCode>
					<cac:AddressLine>
						<cbc:Line>Cra. 50 #97a Sur-180 a 97a Sur-394</cbc:Line>
					</cac:AddressLine>
					<cac:Country>
						<cbc:IdentificationCode>CO</cbc:IdentificationCode>
						<cbc:Name languageID=\"es\">Colombia</cbc:Name>
					</cac:Country>
				</cac:Address>
			</cac:PhysicalLocation>
			<cac:PartyTaxScheme>
				<cbc:RegistrationName>Cadena S.A.</cbc:RegistrationName>
				<cbc:CompanyID schemeID=\"0\" schemeName=\"31\" schemeAgencyID=\"195\" schemeAgencyName=\"CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)\">890930534</cbc:CompanyID>
				<cbc:TaxLevelCode listName=\"05\">O-99</cbc:TaxLevelCode>
				<cac:RegistrationAddress>
					<cbc:ID>05380</cbc:ID>
					<cbc:CityName>LA ESTRELLA</cbc:CityName>
					<cbc:PostalZone>055468</cbc:PostalZone>
					<cbc:CountrySubentity>Antioquia</cbc:CountrySubentity>
					<cbc:CountrySubentityCode>05</cbc:CountrySubentityCode>
					<cac:AddressLine>
						<cbc:Line>Cra. 50 #97a Sur-180 a 97a Sur-394</cbc:Line>
					</cac:AddressLine>
					<cac:Country>
						<cbc:IdentificationCode>CO</cbc:IdentificationCode>
						<cbc:Name languageID=\"es\">Colombia</cbc:Name>
					</cac:Country>
				</cac:RegistrationAddress>
				<cac:TaxScheme>
					<cbc:ID>01</cbc:ID>
					<cbc:Name>IVA</cbc:Name>
				</cac:TaxScheme>
			</cac:PartyTaxScheme>
			<cac:PartyLegalEntity>
				<cbc:RegistrationName>Cadena S.A.</cbc:RegistrationName>
				<cbc:CompanyID schemeID=\"0\" schemeName=\"31\" schemeAgencyID=\"195\" schemeAgencyName=\"CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)\">890930534</cbc:CompanyID>
				<cac:CorporateRegistrationScheme>
					<cbc:ID>SETP</cbc:ID>
					<cbc:Name>1485596</cbc:Name>
				</cac:CorporateRegistrationScheme>
			</cac:PartyLegalEntity>
			<cac:Contact>
				<cbc:ElectronicMail>leandro.ocampo@cadena.com.co</cbc:ElectronicMail>
			</cac:Contact>
		</cac:Party>
	</cac:AccountingSupplierParty>
	<cac:AccountingCustomerParty>
		<cbc:AdditionalAccountID>1</cbc:AdditionalAccountID>
		<cac:Party>
			<cac:PartyName>
				<cbc:Name>ADQUIRIENTE DE EJEMPLO</cbc:Name>
			</cac:PartyName>
			<cac:PhysicalLocation>
				<cac:Address>
					<cbc:ID>66001</cbc:ID>
					<cbc:CityName>PEREIRA</cbc:CityName>
					<cbc:PostalZone>54321</cbc:PostalZone>
					<cbc:CountrySubentity>Risaralda</cbc:CountrySubentity>
					<cbc:CountrySubentityCode>66</cbc:CountrySubentityCode>
					<cac:AddressLine>
						<cbc:Line>CR 9 A N0 99 - 07 OF 802</cbc:Line>
					</cac:AddressLine>
					<cac:Country>
						<cbc:IdentificationCode>CO</cbc:IdentificationCode>
						<cbc:Name languageID=\"es\">Colombia</cbc:Name>
					</cac:Country>
				</cac:Address>
			</cac:PhysicalLocation>
			<cac:PartyTaxScheme>
				<cbc:RegistrationName>ADQUIRIENTE DE EJEMPLO</cbc:RegistrationName>
				<cbc:CompanyID schemeID=\"3\" schemeName=\"31\" schemeAgencyID=\"195\" schemeAgencyName=\"CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)\">1017173008</cbc:CompanyID>
				<cbc:TaxLevelCode listName=\"05\">O-99</cbc:TaxLevelCode>
				<cac:RegistrationAddress>
					<cbc:ID>66001</cbc:ID>
					<cbc:CityName>PEREIRA</cbc:CityName>
					<cbc:PostalZone>54321</cbc:PostalZone>
					<cbc:CountrySubentity>Risaralda</cbc:CountrySubentity>
					<cbc:CountrySubentityCode>66</cbc:CountrySubentityCode>
					<cac:AddressLine>
						<cbc:Line>CR 9 A N0 99 - 07 OF 802</cbc:Line>
					</cac:AddressLine>
					<cac:Country>
						<cbc:IdentificationCode>CO</cbc:IdentificationCode>
						<cbc:Name languageID=\"es\">Colombia</cbc:Name>
					</cac:Country>
				</cac:RegistrationAddress>
				<cac:TaxScheme>
					<cbc:ID>01</cbc:ID>
					<cbc:Name>IVA</cbc:Name>
				</cac:TaxScheme>
			</cac:PartyTaxScheme>
			<cac:PartyLegalEntity>
				<cbc:RegistrationName>ADQUIRIENTE DE EJEMPLO</cbc:RegistrationName>
				<cbc:CompanyID schemeID=\"3\" schemeName=\"31\" schemeAgencyID=\"195\" schemeAgencyName=\"CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)\">1017173008</cbc:CompanyID>
				<cac:CorporateRegistrationScheme>
					<cbc:Name>1485596</cbc:Name>
				</cac:CorporateRegistrationScheme>
			</cac:PartyLegalEntity>
			<cac:Contact>
				<cbc:ElectronicMail>leandro.sys89@gmail.com</cbc:ElectronicMail>
			</cac:Contact>
		</cac:Party>
	</cac:AccountingCustomerParty>
	<cac:PaymentMeans>
		<cbc:ID>1</cbc:ID>
		<cbc:PaymentMeansCode>10</cbc:PaymentMeansCode>
		<cbc:PaymentID>Efectivo</cbc:PaymentID>
	</cac:PaymentMeans>
	<cac:TaxTotal>
		<cbc:TaxAmount currencyID=\"COP\">19000.00</cbc:TaxAmount>
		<cac:TaxSubtotal>
			<cbc:TaxableAmount currencyID=\"COP\">100000.00</cbc:TaxableAmount>
			<cbc:TaxAmount currencyID=\"COP\">19000.00</cbc:TaxAmount>
			<cac:TaxCategory>
				<cbc:Percent>19.00</cbc:Percent>
				<cac:TaxScheme>
					<cbc:ID>01</cbc:ID>
					<cbc:Name>IVA</cbc:Name>
				</cac:TaxScheme>
			</cac:TaxCategory>
		</cac:TaxSubtotal>
	</cac:TaxTotal>
	<cac:LegalMonetaryTotal>
		<cbc:LineExtensionAmount currencyID=\"COP\">100000.00</cbc:LineExtensionAmount>
		<cbc:TaxExclusiveAmount currencyID=\"COP\">100000.00</cbc:TaxExclusiveAmount>
		<cbc:TaxInclusiveAmount currencyID=\"COP\">119000.00</cbc:TaxInclusiveAmount>
		<cbc:PayableAmount currencyID=\"COP\">119000.00</cbc:PayableAmount>
	</cac:LegalMonetaryTotal>
	<cac:InvoiceLine>
		<cbc:ID>1</cbc:ID>
		<cbc:InvoicedQuantity>1.00</cbc:InvoicedQuantity>
		<cbc:LineExtensionAmount currencyID=\"COP\">100000.00</cbc:LineExtensionAmount>
		<cac:TaxTotal>
			<cbc:TaxAmount currencyID=\"COP\">19000.00</cbc:TaxAmount>			
			<cac:TaxSubtotal>
				<cbc:TaxableAmount currencyID=\"COP\">100000.00</cbc:TaxableAmount>
				<cbc:TaxAmount currencyID=\"COP\">19000.00</cbc:TaxAmount>
				<cac:TaxCategory>
					<cbc:Percent>19.00</cbc:Percent>
					<cac:TaxScheme>
						<cbc:ID>01</cbc:ID>
						<cbc:Name>IVA</cbc:Name>
					</cac:TaxScheme>
				</cac:TaxCategory>
			</cac:TaxSubtotal>
		</cac:TaxTotal>
		<cac:Item>
			<cbc:Description>Frambuesas</cbc:Description>
			<cac:StandardItemIdentification>
				<cbc:ID schemeID=\"999\">03222314-7</cbc:ID>
			</cac:StandardItemIdentification>
		</cac:Item>
		<cac:Price>
			<cbc:PriceAmount currencyID=\"COP\">100000.00</cbc:PriceAmount>
			<cbc:BaseQuantity unitCode=\"EA\">1.00</cbc:BaseQuantity>
		</cac:Price>
	</cac:InvoiceLine>
	<DATA>
		<UBL21>true</UBL21>
		<Partnership>
			<ID>901192048</ID>
			<TechKey>fc8eac422eba16e22ffd8c6f94b3f40a6e38162c</TechKey>
			<SetTestID>b9ca446f-395e-44e2-847d-300e1a0f61fe</SetTestID>
		</Partnership>
	</DATA>
</Invoice>";
        return $xml;
    }

}
