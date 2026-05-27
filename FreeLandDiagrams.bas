Attribute VB_Name = "FreeLandDiagrams"
Option Explicit

'================ ТОЧКА ВХОДА ================
Sub DrawAllDiagrams()
    Dim doc As Visio.Document
    Set doc = Visio.ActiveDocument
    If doc Is Nothing Then Set doc = Visio.Documents.Add("")

    DrawERDiagram doc
    DrawUseCaseDiagram doc
    DrawComponentDiagram doc

    MsgBox "Готово! Создано 3 страницы: ER, Прецеденты, Компоненты.", vbInformation
End Sub

'================ ВСПОМОГАТЕЛЬНЫЕ ================
Private Function NewPage(doc As Visio.Document, nameP As String, wIn As Double, hIn As Double) As Visio.Page
    Dim existing As Visio.Page
    On Error Resume Next
    Set existing = doc.Pages.ItemU(nameP)
    On Error GoTo 0
    If Not existing Is Nothing Then existing.Delete 0

    Dim pg As Visio.Page
    Set pg = doc.Pages.Add
    pg.Name = nameP
    pg.AutoSize = False
    pg.PageSheet.Cells("PageWidth").ResultIU = wIn
    pg.PageSheet.Cells("PageHeight").ResultIU = hIn
    Set NewPage = pg
End Function

' Линия (с опц. стрелкой/пунктиром/подписью)
Private Function Line2(pg As Visio.Page, x1 As Double, y1 As Double, x2 As Double, y2 As Double, _
                       Optional arrow As Boolean = False, Optional dashed As Boolean = False, _
                       Optional txt As String = "", Optional col As String = "RGB(90,130,180)") As Visio.Shape
    Dim l As Visio.Shape
    Set l = pg.DrawLine(x1, y1, x2, y2)
    l.Cells("LineColor").FormulaU = col
    If arrow Then l.Cells("EndArrow").FormulaU = "4"
    If dashed Then l.Cells("LinePattern").FormulaU = "2"
    If Len(txt) > 0 Then
        l.Text = txt
        l.Cells("Char.Size").FormulaU = "7 pt"
        l.Cells("Char.Color").FormulaU = "RGB(60,60,60)"
    End If
    Set Line2 = l
End Function

' Соединить центры двух фигур
Private Function Conn(pg As Visio.Page, a As Visio.Shape, b As Visio.Shape, _
                      Optional arrow As Boolean = False, Optional dashed As Boolean = False, _
                      Optional txt As String = "") As Visio.Shape
    Dim x1 As Double, y1 As Double, x2 As Double, y2 As Double
    x1 = a.Cells("PinX").ResultIU: y1 = a.Cells("PinY").ResultIU
    x2 = b.Cells("PinX").ResultIU: y2 = b.Cells("PinY").ResultIU
    Set Conn = Line2(pg, x1, y1, x2, y2, arrow, dashed, txt)
End Function

Private Sub AddTitle(pg As Visio.Page, x As Double, y As Double, txt As String)
    Dim s As Visio.Shape
    Set s = pg.DrawRectangle(x, y, x + 8, y + 0.4)
    s.Text = txt
    s.Cells("Char.Size").FormulaU = "13 pt"
    s.Cells("Char.Style").FormulaU = "1"
    s.Cells("LinePattern").FormulaU = "0"
    s.Cells("FillPattern").FormulaU = "0"
    s.Cells("Para.HorzAlign").FormulaU = "0"
End Sub

'================ 1. ER-ДИАГРАММА ================
Private Function ERTable(pg As Visio.Page, x As Double, yTop As Double, w As Double, _
                         title As String, fields As String, nLines As Integer) As Visio.Shape
    Dim hH As Double: hH = 0.34
    Dim bH As Double: bH = nLines * 0.16 + 0.12

    Dim hdr As Visio.Shape
    Set hdr = pg.DrawRectangle(x, yTop - hH, x + w, yTop)
    hdr.Text = title
    hdr.Cells("Char.Size").FormulaU = "8 pt"
    hdr.Cells("Char.Style").FormulaU = "1"
    hdr.Cells("FillForegnd").FormulaU = "RGB(210,228,250)"
    hdr.Cells("LineColor").FormulaU = "RGB(90,140,200)"
    hdr.Cells("Char.Color").FormulaU = "RGB(20,50,90)"

    Dim bdy As Visio.Shape
    Set bdy = pg.DrawRectangle(x, yTop - hH - bH, x + w, yTop - hH)
    bdy.Text = fields
    bdy.Cells("Char.Size").FormulaU = "7 pt"
    bdy.Cells("Para.HorzAlign").FormulaU = "0"
    bdy.Cells("VerticalAlign").FormulaU = "0"
    bdy.Cells("FillForegnd").FormulaU = "RGB(255,255,255)"
    bdy.Cells("LineColor").FormulaU = "RGB(90,140,200)"
    bdy.Cells("Char.Color").FormulaU = "RGB(40,40,40)"

    Set ERTable = hdr
End Function

Private Sub DrawERDiagram(doc As Visio.Document)
    Dim pg As Visio.Page
    Set pg = NewPage(doc, "ER", 16, 10)

    Dim tUsers As Visio.Shape, tProj As Visio.Shape, tCat As Visio.Shape, tApp As Visio.Shape
    Dim tCatReq As Visio.Shape, tChats As Visio.Shape, tMsg As Visio.Shape, tPay As Visio.Shape
    Dim tComm As Visio.Shape, tRev As Visio.Shape, tCompl As Visio.Shape

    Set tUsers = ERTable(pg, 6.4, 9.5, 2.6, "Users", _
        "PK id" & vbCrLf & "full_name" & vbCrLf & "login" & vbCrLf & "password" & vbCrLf & _
        "phone" & vbCrLf & "avatar" & vbCrLf & "role" & vbCrLf & "balance" & vbCrLf & _
        "rating" & vbCrLf & "telegram" & vbCrLf & "github" & vbCrLf & "is_banned", 12)

    Set tProj = ERTable(pg, 3.2, 9.5, 2.6, "Projects", _
        "PK id" & vbCrLf & "title" & vbCrLf & "description" & vbCrLf & "budget" & vbCrLf & _
        "deadline" & vbCrLf & "status" & vbCrLf & "FK customer_id" & vbCrLf & _
        "FK freelancer_id" & vbCrLf & "FK category_id", 9)

    Set tCat = ERTable(pg, 0.3, 9.5, 2.4, "Categories", _
        "PK id" & vbCrLf & "name" & vbCrLf & "description", 3)

    Set tApp = ERTable(pg, 9.6, 9.5, 2.4, "Applications", _
        "PK id" & vbCrLf & "FK user_id" & vbCrLf & "FK project_id" & vbCrLf & "status", 4)

    Set tCatReq = ERTable(pg, 12.6, 9.5, 2.9, "CategoryRequests", _
        "PK id" & vbCrLf & "FK user_id" & vbCrLf & "name" & vbCrLf & "description" & vbCrLf & _
        "status" & vbCrLf & "category_id" & vbCrLf & "reviewed_by", 7)

    Set tMsg = ERTable(pg, 0.3, 4.7, 2.6, "Messages", _
        "PK id" & vbCrLf & "FK chat_id" & vbCrLf & "FK author_id" & vbCrLf & _
        "text (encrypted)" & vbCrLf & "attachment_path" & vbCrLf & "attachment_name" & vbCrLf & _
        "time" & vbCrLf & "read_at", 8)

    Set tChats = ERTable(pg, 3.4, 4.7, 2.6, "Chats", _
        "PK id" & vbCrLf & "FK author_id" & vbCrLf & "FK interlocutor_id" & vbCrLf & _
        "FK project_id" & vbCrLf & "token", 5)

    Set tPay = ERTable(pg, 6.4, 4.7, 2.6, "Payments", _
        "PK id" & vbCrLf & "FK customer_id" & vbCrLf & "FK freelancer_id" & vbCrLf & _
        "FK project_id" & vbCrLf & "amount" & vbCrLf & "commission" & vbCrLf & _
        "type" & vbCrLf & "status", 8)

    Set tComm = ERTable(pg, 9.6, 4.7, 2.4, "Comments", _
        "PK id" & vbCrLf & "text" & vbCrLf & "FK author_id" & vbCrLf & "FK user_id" & vbCrLf & "rating", 5)

    Set tRev = ERTable(pg, 12.6, 4.7, 2.9, "Reviews", _
        "PK id" & vbCrLf & "name" & vbCrLf & "email" & vbCrLf & "text" & vbCrLf & _
        "status" & vbCrLf & "ip_address" & vbCrLf & "user_agent", 7)

    Set tCompl = ERTable(pg, 6.4, 2.0, 2.8, "Complaints", _
        "PK id" & vbCrLf & "FK project_id" & vbCrLf & "FK freelancer_id" & vbCrLf & _
        "FK customer_id" & vbCrLf & "text" & vbCrLf & "status" & vbCrLf & "FK chat_id", 7)

    Conn pg, tUsers, tProj, False, False, "1 .. 0..*"
    Conn pg, tCat, tProj, False, False, "1 .. 0..*"
    Conn pg, tUsers, tApp, False, False, "1 .. 0..*"
    Conn pg, tProj, tApp, False, False, "1 .. 0..*"
    Conn pg, tUsers, tCatReq, False, False, "1 .. 0..*"
    Conn pg, tProj, tChats, False, False, "1 .. 1"
    Conn pg, tChats, tMsg, False, False, "1 .. 0..*"
    Conn pg, tUsers, tMsg, False, False, "1 .. 0..*"
    Conn pg, tProj, tPay, False, False, "1 .. 0..*"
    Conn pg, tUsers, tPay, False, False, "1 .. 0..*"
    Conn pg, tUsers, tComm, False, False, "1 .. 0..*"
    Conn pg, tUsers, tRev, False, False, ""
    Conn pg, tProj, tCompl, False, False, "1 .. 0..*"
    Conn pg, tUsers, tCompl, False, False, "1 .. 0..*"

    AddTitle pg, 0.3, 9.55, "ER-диаграмма базы данных — FreeLand"
End Sub

'================ 2. ДИАГРАММА ПРЕЦЕДЕНТОВ ================
Private Function Oval2(pg As Visio.Page, cx As Double, cy As Double, w As Double, h As Double, txt As String) As Visio.Shape
    Dim s As Visio.Shape
    Set s = pg.DrawOval(cx - w / 2, cy - h / 2, cx + w / 2, cy + h / 2)
    s.Text = txt
    s.Cells("Char.Size").FormulaU = "8 pt"
    s.Cells("FillForegnd").FormulaU = "RGB(255,255,255)"
    s.Cells("LineColor").FormulaU = "RGB(140,140,140)"
    Set Oval2 = s
End Function

' Актёр-человечек, возвращает фигуру головы (для соединений)
Private Function Actor(pg As Visio.Page, cx As Double, cy As Double, nm As String) As Visio.Shape
    Dim head As Visio.Shape
    Set head = pg.DrawOval(cx - 0.13, cy + 0.45, cx + 0.13, cy + 0.71)
    head.Cells("FillForegnd").FormulaU = "RGB(255,255,255)"
    head.Cells("LineColor").FormulaU = "RGB(120,120,120)"
    Line2 pg, cx, cy + 0.45, cx, cy + 0.05, , , , "RGB(120,120,120)"
    Line2 pg, cx - 0.22, cy + 0.32, cx + 0.22, cy + 0.32, , , , "RGB(120,120,120)"
    Line2 pg, cx, cy + 0.05, cx - 0.18, cy - 0.28, , , , "RGB(120,120,120)"
    Line2 pg, cx, cy + 0.05, cx + 0.18, cy - 0.28, , , , "RGB(120,120,120)"
    Dim lbl As Visio.Shape
    Set lbl = pg.DrawRectangle(cx - 0.7, cy - 0.6, cx + 0.7, cy - 0.32)
    lbl.Text = nm
    lbl.Cells("Char.Size").FormulaU = "9 pt"
    lbl.Cells("LinePattern").FormulaU = "0"
    lbl.Cells("FillPattern").FormulaU = "0"
    Set Actor = head
End Function

Private Sub DrawUseCaseDiagram(doc As Visio.Document)
    Dim pg As Visio.Page
    Set pg = NewPage(doc, "Прецеденты", 13, 9)

    ' Системная рамка
    Dim bnd As Visio.Shape
    Set bnd = pg.DrawRectangle(1.6, 0.4, 11.0, 8.4)
    bnd.Text = "Система FreeLand"
    bnd.Cells("Char.Size").FormulaU = "10 pt"
    bnd.Cells("Para.HorzAlign").FormulaU = "0"
    bnd.Cells("VerticalAlign").FormulaU = "0"
    bnd.Cells("FillPattern").FormulaU = "0"
    bnd.Cells("LineColor").FormulaU = "RGB(210,170,40)"
    bnd.Cells("LineWeight").FormulaU = "1.5 pt"

    Dim aUser As Visio.Shape, aAdmin As Visio.Shape
    Set aUser = Actor(pg, 12.2, 6.0, "Пользователь")
    Set aAdmin = Actor(pg, 0.8, 2.6, "Администратор")

    ' Прецеденты пользователя
    Dim ucReg As Visio.Shape, ucAuth As Visio.Shape, ucPass As Visio.Shape, ucBrowse As Visio.Shape
    Dim ucCreate As Visio.Shape, ucApply As Visio.Shape, ucPay As Visio.Shape, ucTransfer As Visio.Shape
    Dim ucCancel As Visio.Shape, ucChat As Visio.Shape, ucComplain As Visio.Shape, ucReview As Visio.Shape
    Set ucReg = Oval2(pg, 9.4, 7.9, 1.9, 0.7, "Регистрация")
    Set ucAuth = Oval2(pg, 9.4, 6.9, 1.9, 0.7, "Авторизация")
    Set ucPass = Oval2(pg, 7.1, 7.9, 1.9, 0.7, "Восстановление пароля")
    Set ucBrowse = Oval2(pg, 4.6, 7.6, 1.9, 0.7, "Просмотр проектов")
    Set ucCreate = Oval2(pg, 6.6, 6.6, 1.9, 0.7, "Создать проект")
    Set ucApply = Oval2(pg, 4.6, 6.4, 1.9, 0.7, "Откликнуться")
    Set ucPay = Oval2(pg, 8.8, 5.6, 1.9, 0.7, "Оплатить проект")
    Set ucTransfer = Oval2(pg, 9.6, 4.6, 1.9, 0.7, "Перевод средств")
    Set ucCancel = Oval2(pg, 6.7, 5.4, 1.9, 0.7, "Отменить заказ")
    Set ucChat = Oval2(pg, 4.6, 5.2, 1.9, 0.7, "Чат / переписка")
    Set ucComplain = Oval2(pg, 4.6, 4.1, 1.9, 0.7, "Пожаловаться")
    Set ucReview = Oval2(pg, 6.7, 4.2, 1.9, 0.7, "Оставить отзыв")

    ' Прецеденты администратора
    Dim ucMUsers As Visio.Shape, ucMProj As Visio.Shape, ucMCat As Visio.Shape
    Dim ucModer As Visio.Shape, ucAccept As Visio.Shape, ucComm As Visio.Shape
    Set ucMUsers = Oval2(pg, 3.3, 3.1, 2.0, 0.7, "Управление пользователями")
    Set ucMProj = Oval2(pg, 3.3, 2.2, 2.0, 0.7, "Управление проектами")
    Set ucMCat = Oval2(pg, 5.6, 2.6, 2.0, 0.7, "Управление категориями")
    Set ucModer = Oval2(pg, 5.6, 1.7, 2.0, 0.7, "Модерация отзывов")
    Set ucAccept = Oval2(pg, 7.9, 2.2, 2.0, 0.7, "Принять жалобу")
    Set ucComm = Oval2(pg, 3.3, 1.3, 2.0, 0.7, "Настройка комиссии")

    ' Связи пользователя
    Conn pg, aUser, ucReg
    Conn pg, aUser, ucAuth
    Conn pg, aUser, ucPass
    Conn pg, aUser, ucBrowse
    Conn pg, aUser, ucCreate
    Conn pg, aUser, ucApply
    Conn pg, aUser, ucPay
    Conn pg, aUser, ucCancel
    Conn pg, aUser, ucChat
    Conn pg, aUser, ucComplain
    Conn pg, aUser, ucReview

    ' include
    Conn pg, ucPay, ucTransfer, True, True, "<<include>>"
    Conn pg, ucReg, ucAuth, True, True, "<<include>>"

    ' Связи администратора
    Conn pg, aAdmin, ucAuth
    Conn pg, aAdmin, ucMUsers
    Conn pg, aAdmin, ucMProj
    Conn pg, aAdmin, ucMCat
    Conn pg, aAdmin, ucModer
    Conn pg, aAdmin, ucAccept
    Conn pg, aAdmin, ucComm

    AddTitle pg, 1.6, 8.5, "Диаграмма прецедентов — FreeLand"
End Sub

'================ 3. КОМПОНЕНТНАЯ ДИАГРАММА ================
Private Function CompBox(pg As Visio.Page, x As Double, y As Double, w As Double, h As Double, _
                         stereo As String, nm As String) As Visio.Shape
    Dim s As Visio.Shape
    Set s = pg.DrawRectangle(x, y, x + w, y + h)
    s.Text = "<<" & stereo & ">>" & vbCrLf & nm
    s.Cells("Char.Size").FormulaU = "8 pt"
    s.Cells("FillForegnd").FormulaU = "RGB(235,243,255)"
    s.Cells("LineColor").FormulaU = "RGB(80,130,200)"
    s.Cells("Char.Color").FormulaU = "RGB(20,50,90)"
    ' значок компонента (справа сверху)
    Dim g1 As Visio.Shape
    Set g1 = pg.DrawRectangle(x + w - 0.34, y + h - 0.26, x + w - 0.1, y + h - 0.08)
    g1.Cells("FillForegnd").FormulaU = "RGB(235,243,255)"
    g1.Cells("LineColor").FormulaU = "RGB(80,130,200)"
    pg.DrawRectangle(x + w - 0.4, y + h - 0.13, x + w - 0.28, y + h - 0.09).Cells("FillForegnd").FormulaU = "RGB(235,243,255)"
    pg.DrawRectangle(x + w - 0.4, y + h - 0.22, x + w - 0.28, y + h - 0.18).Cells("FillForegnd").FormulaU = "RGB(235,243,255)"
    Set CompBox = s
End Function

Private Function SubsysBox(pg As Visio.Page, x As Double, y As Double, w As Double, h As Double, nm As String) As Visio.Shape
    Dim s As Visio.Shape
    Set s = pg.DrawRectangle(x, y, x + w, y + h)
    s.Text = "<<subsystem>>  " & nm
    s.Cells("Char.Size").FormulaU = "9 pt"
    s.Cells("Para.HorzAlign").FormulaU = "0"
    s.Cells("VerticalAlign").FormulaU = "0"
    s.Cells("FillPattern").FormulaU = "0"
    s.Cells("LineColor").FormulaU = "RGB(80,130,200)"
    Set SubsysBox = s
End Function

Private Function DbCylinder(pg As Visio.Page, x As Double, y As Double, w As Double, h As Double, nm As String) As Visio.Shape
    Dim body As Visio.Shape
    Set body = pg.DrawRectangle(x, y, x + w, y + h - 0.13)
    body.Cells("FillForegnd").FormulaU = "RGB(120,170,230)"
    body.Cells("LineColor").FormulaU = "RGB(60,110,180)"
    body.Text = nm
    body.Cells("Char.Color").FormulaU = "RGB(255,255,255)"
    body.Cells("Char.Size").FormulaU = "8 pt"
    Dim topE As Visio.Shape
    Set topE = pg.DrawOval(x, y + h - 0.26, x + w, y + h)
    topE.Cells("FillForegnd").FormulaU = "RGB(150,195,245)"
    topE.Cells("LineColor").FormulaU = "RGB(60,110,180)"
    Set DbCylinder = body
End Function

Private Sub DrawComponentDiagram(doc As Visio.Document)
    Dim pg As Visio.Page
    Set pg = NewPage(doc, "Компоненты", 11.5, 8.5)

    Dim cUI As Visio.Shape
    Set cUI = CompBox(pg, 4.3, 7.0, 2.8, 0.9, "component", "Пользовательский интерфейс (Vue SPA)")

    ' Подсистема Ядро (Laravel API)
    Dim sCore As Visio.Shape
    Set sCore = SubsysBox(pg, 3.8, 1.6, 7.3, 4.6, "Ядро (Laravel API)")
    Dim cCtrl As Visio.Shape, cServ As Visio.Shape, cModels As Visio.Shape
    Set cCtrl = CompBox(pg, 4.2, 5.0, 2.4, 0.9, "component", "Контроллеры")
    Set cServ = CompBox(pg, 4.2, 3.5, 2.4, 0.9, "component", "Сервисы (эскроу, почта, шифрование)")
    Set cModels = CompBox(pg, 7.9, 3.5, 2.6, 0.9, "component", "Модели (Eloquent)")

    ' Подсистема Инфраструктура
    Dim sInfra As Visio.Shape
    Set sInfra = SubsysBox(pg, 0.3, 1.6, 3.2, 3.0, "Инфраструктура")
    Dim cLib As Visio.Shape, cDb As Visio.Shape
    Set cLib = CompBox(pg, 0.5, 2.9, 1.4, 0.9, "library", "Пакеты: Sanctum, PHPMailer, dompdf")
    Set cDb = DbCylinder(pg, 2.2, 2.7, 1.1, 1.1, "MySQL")

    ' Связи
    Conn pg, cUI, cCtrl, True, False, "HTTP / REST API"
    Conn pg, cCtrl, cServ, True, False, "использует"
    Conn pg, cServ, cModels, True, False, "доступ к данным"
    Conn pg, cModels, cDb, True, False, "ORM-запросы"
    Conn pg, cServ, cLib, True, True, "использует"

    AddTitle pg, 0.3, 8.0, "Компонентная диаграмма — FreeLand"
End Sub
