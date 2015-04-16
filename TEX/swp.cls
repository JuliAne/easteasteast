% -----------------------------------------------------------------------
% pi1.cls
% Based on pi1-muster.tex and defs.tex used before 2003 for
% PI 1 in Bremen.
% Adapted by Thomas Röfer and Ulrich Hannemann

\NeedsTeXFormat{LaTeX2e}

\ProvidesClass{pi1}
\LoadClass[11pt,a4paper,twoside]{article}
\usepackage{ngerman}
\usepackage[latin1]{inputenc}
\usepackage{graphicx}
\usepackage{moreverb}
\usepackage{fancyhdr}

% -----------------------------------------------------------------------
% Page layout

% enlarge page
\setlength{\textwidth}{16cm}
\setlength{\topmargin}{-1cm}
\setlength{\evensidemargin}{0cm}
\setlength{\oddsidemargin}{0cm}
\setlength{\textheight}{24cm}

% skip between paragraphs
\setlength{\parskip}{1ex}
% ... and no indentation at start of a new paragraph
\setlength{\parindent}{0ex}

\newcommand{\thissemester}{SS 2015}
\newcommand{\thistitle}{Softwaretechnikpraktikum}

\pagestyle{fancy}
\thispagestyle{empty}

% -----------------------------------------------------------------------
% Main macros
%
% here the title is defined.
% Parameters:
% #1: Titel 
% #2: Erstellungsdatum
% #3: Autor 


\renewcommand{\maketitle}[9]
          {\@maketitle{#1}{#2}{#3}{#4}{#5}{#6}{#7}{#8}{#9}}
\renewcommand\thesection{Aufgabe \arabic{section}}
\newcommand\skipsection{\stepcounter{section}}

% -----------------------------------------------------------------------
% Misc macros

\newcommand{\grenzop}[3]{\mathop{#1}\limits^{#2}_{#3}}
\newcommand{\langrechtspfeil}[2]{\grenzop{\longrightarrow}{#1}{#2}}
\newcommand{\bob}[0]{\hspace*{\fill}$\Box$}

% -----------------------------------------------------------------------

\renewcommand{\@maketitle}[9]
{
    \begin{tabular}[t]{lr}
      \begin{tabular}[t]{l}
        \begin{tabular}[t]{l@{\hskip 10mm}l}
            Softwaretechnikpraktikum & Gruppe: swp15.aae \hspace{4ex} \\
            \thissemester  &  Betreuer: Prof. Gräbe \\
            Datum: {#2}	&	Tutor: Klemens Schölhorn\\
        \end{tabular} \vspace{20mm} \\
        {\Huge\sf {#1}} \\[1.5ex]
        %{\Large Softwaretechnikpraktikum} \\[1ex]
        %{\Large Abgabe: {#3}} \\[1ex]
      \end{tabular}
      &
      \begin{tabular}[t]{l}
     	  Projektteam:\\
          {#3}\\
          {#4}\\
          {#5}\\
          {#6}\\
          {#7}\\
          {#8}\\
          {#9}\\
      \end{tabular} \\[2ex]
    \end{tabular}\\
    \rule{\linewidth}{1pt}

% -----------------------------------------------------------------------
% Create headings (Thanks to Elmar Loos)

    \fancyhead{} %clear all fields
    \fancyhead[LO,RE]{swp15.aae}
    \fancyhead[RO,LE]{Max Mustermann, {#2}}
    \fancyfoot{} %clear all fields
    \fancyfoot[RO,LE]{\thepage}
}

\endinput
