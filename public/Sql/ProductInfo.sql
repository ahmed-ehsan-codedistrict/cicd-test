
create FUNCTION [dbo].[ProductInfo] (
	@companyNo CHAR(30),
    @styleNo CHAR(30),
    @bit int
)
RETURNS nvarchar(max) AS
BEGIN
	DECLARE @return_value nvarchar(max);
    DECLARE @QualityAttribute nvarchar(max);
    DECLARE @Rowcnt int;
    Declare @srtCount int;
    if(@bit=1)
      begin
	         SET @return_value = (SELECT string_agg(REPLACE(RTRIM(season),'',''),'|' )
                         FROM   ProdPLM plm
                         where  plm.CompanyNo = @companyNo and plm.Style = @styleNo

                          );
      end;
   if(@bit=2)
       begin
	         SET @return_value = (SELECT  string_agg(REPLACE(RTRIM(Market),'',''),'|' )
                         FROM   ProdPLM plm
                         where  plm.CompanyNo = @companyNo and plm.Style = @styleNo

                          );
      end;
      if(@bit=3)
       begin
	         SET @return_value = (SELECT string_agg(REPLACE(FabType,'',''),'|' )
                         FROM   ProdPLM plm
                         where  plm.CompanyNo = @companyNo and plm.Style = @styleNo

                          );
      end;
      if(@bit=4)
       begin
	         SET @return_value = (SELECT  string_agg(REPLACE(RTRIM(FabricName),'',''),'|' ) FabricName
                         FROM   ProdPLM plm
                         where  plm.CompanyNo = @companyNo and plm.Style = @styleNo

                          );
        end;
       if(@bit=5)
         begin
            set @return_value = (select
                                  string_agg(REPLACE(concat(RTRIM(attribname),':',RTRIM(attribval)),'',''),'|' ) as QA from  ProdFit plm
                                  where  plm.CompanyNo = @companyNo and plm.Style = @styleNo
                                );
         end;
         if(@bit=6)
            begin
                set @return_value = (Select
                                        string_agg(Replace(concat('ColorCode',':',cm.Color,'-','ColorName',':',cm.CRDS3J,'-','ColorExDes',':',cm.CDES3J,'-','ColorNLC',':',cm.NCLR3J),'',''),'|') as cCodeName
                                        from

                                        dbo.PRDTMS0 pd
                                        inner join dbo.COLRMS0 cm
                                        on pd.Color = cm.Color

                                        where pd.CompanyNo=@companyNo and cm.CompanyNo=@companyNo and pd.Style =  @styleNo
                                    );
            end;
             if(@bit=7)
                begin
                    set @return_value = (SELECT
                                            STRING_AGG(CONCAT(ucp.UPCN5R,'-',ucp.NCLR5R),'|') AS UCPNXC
                                            from
                                            dbo.UPCXRF0 ucp
                                            where ucp.CompanyNo=@companyNo and ucp.Style=@styleNo
                                        );
                end;

    RETURN @return_value;
END;


